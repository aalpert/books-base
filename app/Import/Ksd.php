<?php

namespace App\Import;

use App\Author;
use App\Book;
use App\BookPrice;
use App\Category;
use App\Import;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Ksd extends Model
{
    public static function process($results, $importId)
    {
        $import = Import::find($importId);
        $limitations = $import->params['limit_publishers'];
//        dd($limitations);
        foreach ($results as $raw) {
            $import->total++;
            if (//empty(trim($raw['reference'])) ||
                empty(trim($raw['isbn'])) ||
                empty($raw['price']) ||
                !is_numeric($raw['price']) ||
                // filter out by Publisher
                (!empty($limitations) && count($limitations) && !in_array(mb_strtolower(trim($raw['publisher'])), $limitations))
            ) {
                $import->skipped++;
                $import->update();
                continue;
            }
            $sku = Book::skuFromIsbn($raw['isbn']);

            $books = Book::where('sku', $sku)->get();
            $raw['available_at'] = (preg_match('^\d{1,2}\/\d{1,2}\/\d{4}$^', $raw['available_at']))
                ? Carbon::createFromFormat('n/j/Y', $raw['available_at'])
                : Carbon::now();


            if ($books->count() > 0) {
                // Updating existing
                foreach ($books->all() as $book) {
                    $import->updateBook($book, $raw);
                }
            } else {
                // Creating new
                $raw = Ksd::prepare($raw);
                $book = new Book;
                $book->prepare($raw)->save();
                $book->updatePrices([$import->source_id => [
                    'price' => $raw['price'],
                    'available_at' => $raw['available_at'],
                ]]);
                $book->attach($raw);
                $import->created++;
                $import->addLog($book, 'created', $raw['price']);
                $import->update();
            }
        }
    }

    public static function extract($html, $needle, $end = '<BR>')
    {
        if (!stristr($html, $needle)) {
            return null;
        }
        $str = str_after($html, $needle);
        $str = str_before($str, $end);
        return mb_convert_encoding(trim($str), 'UTF-8', 'UTF-8');
    }

    /**
     * This method is to process the EXMO specific pricelist
     * @param $raw
     * @return array
     */
    public static function prepare($raw)
    {
        if (!empty(trim($raw['cat1']))) {
            $cat1 = Category::firstOrCreate(['title' => trim($raw['cat1'])]);
            $categories[] = $cat1->title;
        }

        if (!empty(trim($raw['cat2']))) {
            $cat2 = Category::firstOrCreate(['title' => trim($raw['cat2'])]);
            $cat2->makeChildOf($cat1);
            $categories[] = $cat2->title;
        }
//if (preg_match('^\d{1,2}\/\d{1,2}\/\d{4}$^', 'новинка')) {echo 'yes';} else {echo 'no';}

        $book = [
            'title' => mb_convert_encoding(substr(trim($raw['title']), 0, 255), 'UTF-8', 'UTF-8'),
            'author' => trim($raw['author']),
            'price' => BookPrice::format($raw['price']),
            'description' => null,
            'image' => null,
            'year' => (int)$raw['year'],
            'availability' => 'A',

            'category' => implode('||', $categories),
            'series' => trim($raw['series']),
            'publisher' => 'Клуб Семейного Досуга', //trim($raw['publisher']),

            'details' => [
                'format' => trim($raw['format']),
                'isbn' => trim($raw['isbn']),
                'pages' => (int)$raw['pages'],
                'bookbinding' => trim($raw['bookbinding']),
                'language' => trim($raw['language']),
                'weight' => trim($raw['weight']),
            ],
        ];

        $book['available_at'] = (preg_match('^\d{1,2}\/\d{1,2}\/\d{4}$^', $raw['available_at']))
            ? Carbon::createFromFormat('n/j/Y', $raw['available_at'])
            : Carbon::now();

        $raw['reference'] = trim($raw['reference']);
        if (empty($raw['reference'])) {
            return $book;
        }
        @$html = file_get_contents($raw['reference']);
        if (empty($html)) {
            return $book;
        }

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXpath($dom);

        $node = $xpath->query('//div[@class="goods-descrp"]');
        if (count($node) && !is_null($node[0])) {
            $book['description'] = (nl2br(trim($node[0]->nodeValue)));
        } else {
            return $book;
        }

        //params
        $node = $xpath->query('//ul[@class="goods-short"]');
        $pars = nl2br($node[0]->nodeValue);
        $authors = static::extract($pars, 'Автор:', '<br');
        if (!empty($authors)) {
            $book['author'] = $authors;
        }

        //image
        $node = $xpath->query('//div[@class="goods-image"]');
        if (count($node) && !is_null($node[0])) {
            $img = $node[0]->getElementsByTagName('img');
            if (count($img)) {
                // Lets try to get big image
                $src = str_replace('/b/', '/', $img[0]->getAttribute('src'));
                $src = str_replace('_b.', '.', $src);
                @$contents = file_get_contents($src);
                if (!empty($contents)) {
                    $book['image'] = Book::imagePathFromRaw($book) . substr($src, strrpos($src, '.'));
                    Storage::put('images/items/' . $book['image'], $contents);
                }
            }
        }

        // Author
        if ($book['author'] && !strpos($book['author'], ',')) {
            // We will try to get some more info..
            $node = $xpath->query('//div[@class="autor-text-color"]');
            if (count($node) && !is_null($node[0])) {
                $name = trim($node[0]->getElementsByTagName('h2')[0]->nodeValue);
                $descr = trim($node[0]->getElementsByTagName('p')[0]->nodeValue);
                if (is_null($author = Author::where('name', trim($raw['author']))->first())) {
                    $author = Author::firstOrCreate([
                        'name' => $name,
                        'description' => $descr,
                    ]);
                } else {
                    $author->name = $name;
                    $author->description = $descr;
                    $author->update();
                }
                $book['author'] = $name;
            }

            $node = $xpath->query('//div[@class="autor-image"]');
            if (count($node) && !is_null($node[0]) && isset($author) && !is_null($author) && empty($author->image)) {
                $img = $node[0]->getElementsByTagName('img');
                if (count($img)) {
                    $src = $img[0]->getAttribute('src');
                    @$contents = file_get_contents($src);
                    if (!empty($contents)) {
                        $i = str_slug($author->name) . '-' . str_random(5) . '-' . time() . substr($src, strrpos($src, '.'));
                        Storage::put('images/authors/' . $i, $contents);
                        $author->image = $i;
                        $author->update();
                    }
                }
            }
        }
        return $book;
    }
}
