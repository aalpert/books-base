<?php

namespace App\Import;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Galina extends Model
{
    public static function process($results, $importId)
    {
        $import = \App\Import::find($importId);
        $limitations = $import->params['limit_publishers'];
//        dd($limitations);
        foreach ($results as $raw) {
            $import->total++;
            if (empty(trim($raw['reference'])) ||
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
            $raw['price'] = number_format((float)$raw['price'], 2, '.', '');

            $sku = \App\Book::skuFromIsbn($raw['isbn']);

            $books = \App\Book::where('sku', '=', $sku)->get();
            if ($books->count() > 0) {
                // Updating existing
                foreach ($books->all() as $book) {
                    // updating source
                    if ($book->source_id != $import->source_id) {
                        $book->source_id = $import->source_id;
                        $book->update();
                    }
                    // Availability
                    if (!$book->available()) {
                        $book->makeAvailable();
                    }
                    // updating price
                    if ((number_format((float)$book->price, 2, '.', '')) != $raw['price']) {
                        $book->price = $raw['price'];
                        $import->updated++;
                        $import->addLog($book, 'updated');
                        $book->update();
                    } else {
                        // Simply update the updated_at field so we can do the clean up later
                        $book->touch();
                    }
                }
            } else {
                // Creating new
                $raw = Galina::prepare($raw);
                $raw['source'] = $import->source_id;
                $book = new \App\Book;
                $book->prepare($raw)->save();
                $book->attach($raw);
                $import->created++;
                $import->addLog($book, 'created');
            }
            $import->update();
        }
    }

    private static function exctract($html, $needle, $end = '<BR>')
    {
        if (!stristr($html, $needle)) {
            return null;
        }
        $str = str_after($html, $needle);
        $str = str_before($str, $end);
        $str = str_after($str, '</a>');
        return mb_convert_encoding(trim($str), 'UTF-8', 'UTF-8');
    }

    /**
     * This method is to process the EXMO specific pricelist
     * @param $raw
     * @return array
     */
    public static function prepare($raw)
    {
        // http://92.39.237.181/Photo/550000/551809.jpg
        $SOURCEURL = 'http://92.39.237.181';
        $reference = (int)$raw->reference;
        // Find out URL
        $reference = $SOURCEURL . '/HTML/' . ((int)($reference / 10000) * 10000) . '/' . $reference . '.html';

        $book = [
            'ref' => $reference,
            'reference' => (int)$raw->reference,
            'title' => trim($raw['title']),
            'author' => trim($raw['author']),
            'isbn' => trim($raw['isbn']),
            'pages' => (int)$raw['pages'],
            'year' => (int)$raw['year'],
            'format' => trim($raw['format']),
            'price' => $raw['price'],
            'category' => null,
            'series' => null,
            'description' => null,
            'image' => null,
            'additional_notes' => $raw['new'],
            'publisher' => trim($raw['publisher']),
        ];

        // Load the content
        @$html = file_get_contents($reference);
        if (empty($html)) {
            return $book;
        }

        $html = iconv('windows-1251', 'utf-8', $html);
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        // Get the title
        $book['title'] = (mb_convert_encoding(substr(trim($dom->getElementsByTagName('h1')->item(0)->textContent), 0, 255), 'UTF-8', 'UTF-8'));

        // Get the description
        $book['description'] = (nl2br(trim($dom->getElementsByTagName('div')->item(0)->textContent)));

        $html = trim(preg_replace('/\s+/', ' ', $html));

        // Get the author
        $book['author'] = $e = static::exctract($html, 'Автор:');

        // Get the genre
        $book['category'] = static::exctract($html, 'Жанр:');

        // Get the series
        $book['series'] = static::exctract($html, 'Серия:');

        // Get cover
        $img = $SOURCEURL . trim($dom->getElementsByTagName('img')->item(0)->getAttribute('src'));
        @$contents = file_get_contents($img);
        if (!empty($contents)) {
            $book['image'] = strtolower(str_slug($book['publisher'])) . '/' . 'book-' . '-' . str_slug($book['title']) . '-' . str_random(5) . time() . substr($img, strrpos($img, '/') + 1);
            Storage::put('images/covers/' . $book['image'], $contents);
        }

        return $book;
    }
}
