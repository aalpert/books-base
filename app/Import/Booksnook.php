<?php

namespace App\Import;

use App\Author;
use App\Book;
use App\Category;
use App\Publisher;
use App\Series;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Booksnook extends Model
{
    public static function process(&$import)
    {
        set_time_limit(0);
        // http://localhost:8888/booksnook.v1/api/export
        $client = new Client();
        $response = $client->get($import->params['host'] . 'api/totals', ['query' =>
            ['token' => $import->params['token']]
        ]);

        if ($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody());
            $total = $res->total;
            $import->total = 0;

            $pn = 0;
            while ($import->total < $total) {
                $pn++;
                Log:info('GETTING PAGE: '.$pn);
                $response = $client->get($import->params['host'] . 'api/export', ['query' => [
                    'pp' => 100,
                    'pn' => $pn,
                    'token' => $import->params['host'],
                ]]);
                if ($response->getStatusCode() == 200) {
                    $res = json_decode($response->getBody());

                    if (count($res->data) < 1) {
                        return;
                    }
                    foreach ($res->data as $importable) {
                        $import->total++;
                        $import->update();

                        if (empty($importable->isbn)) {
                            $import->skipped++;
                            $import->update;
                            continue;
                        }

                        $sku = Book::skuFromIsbn($importable->isbn);
                        $books = Book::where('sku', $sku)->get();

                        if ($books->count() > 0) {
                            // booksnook doesn't provide prices info
                            continue;

                            // Updating existing
//                            foreach ($books->all() as $book) {
//                                $import->updateBook($book, static::raw($importable));
//                            }
                        } else {
                            // Creating new
                            $raw = Booksnook::prepare($importable, $import->params['host']);
                            $book = new Book;
                            $book->prepare($raw)->save();
                            $book->attach($raw);
                            $import->created++;
                            $import->update();
                            $import->addLog($book, 'created');
                        }
                    }
                } else {
                    $import->status = 'failed';
                    return;
                }
            }
        } else {
            $import->status = 'failed';
            return;
        }
    }

    public static function prepare($book, $host)
    {
        $res = static::raw($book);

        // authors
        if (count($book->author)) {
            $res['author'] = array();
            foreach ($book->author as $author) {
                // Let's find an author. if not, we will create one
                if (!$a = Author::where('name', $author->title)->first()) {
                    $a = Author::create([
                        'name' => $author->title,
                        'description' => $author->description,
                    ]);
                }
                $res['author'][] = $a->name;
            }
            $res['author'] = implode(',', $res['author']);
        }

        // publisher
        $res['publisher'] = [];
        foreach($book->publisher as $publisher) {
            if (!$t = Publisher::where('title', $publisher->title)->first()) {
                $t = Publisher::create([
                    'title' => $publisher->title,
                    'description' => $publisher->description,
                ]);
            }
            $res['publisher'][] = $t->title;
        }

        // series
        if (count($book->series)) {
            $res['series'] = $book->series[0]->title;
        }

        // image
        if ($book->image) {
            $host='http://booksnook.com.ua/';
            $img = $host . trim($book->image);
//            dd($img);
            @$contents = file_get_contents($img);
            if (!empty($contents)) {
                $res['image'] = Book::imagePathFromRaw($res) . substr($img, strrpos($img, '.'));
                Storage::put('images/books/' . $res['image'], $contents);
            }
        }

        // gallery

        // category
        if (count($book->category)) {
            $res['category'] = array();
            foreach ($book->category as $cat) {
                $res['category'][] = $cat->title;
            }
            $res['category'] = implode('||', $res['category']);
        }

        return $res;
    }

    public static function raw($book)
    {
        return [
            'title' => $book->title,
            'description' => nl2br(trim($book->description)),
            'pages' => $book->pages,
            'year' => $book->year,
            'format' => $book->format,
            'author' => '',
            'isbn' => trim($book->isbn),
            'price' => (float)$book->price,
            'category' => '',
            'series' => null,
            'image' => null,
            'additional_notes' => null,
            'publisher' => null,
            'bookbinding' => $book->bookbinding,
            'availability' => !empty($book->availability) ? $book->availability : 'A',
        ];
    }
}
