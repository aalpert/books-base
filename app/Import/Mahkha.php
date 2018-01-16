<?php

namespace App\Import;

use App\Book;
use App\BookPrice;
use App\Import;
use Illuminate\Database\Eloquent\Model;

class Mahkha extends Model
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
            $sku = $raw['ean13'];

            $books = Book::where('sku', $sku)->get();

            if ($books->count() > 0) {
                // Updating existing
                foreach ($books->all() as $book) {
                    $import->updateBook($book, $raw);
                }
            } else {
                // Creating new
                $raw = static::prepare($raw);
                $book = new Book;
                $book->prepare($raw)->save();
                $book->updatePrices([$import->source_id => $raw['price']]);
                $book->attach($raw);
                $import->created++;
                $import->addLog($book, 'created', $raw['price']);
                $import->update();
            }
        }
    }

    /**
     * This method is to process the EXMO specific pricelist
     * @param $raw
     * @return array
     */
    public static function prepare($raw)
    {

        $book = [
            'title' => mb_convert_encoding(substr(trim($raw['title']), 0, 255), 'UTF-8', 'UTF-8'),
            'author' => trim($raw['author']),
            'price' => BookPrice::format($raw['price']),
            'description' => null,
            'image' => null,
            'year' => null,
            'availability' => 'A',

            'category' => '',
            'series' => trim($raw['series']),
            'publisher' => 'Клуб Семейного Досуга', //trim($raw['publisher']),

            'details' => [
                'isbn' => trim($raw['isbn']),
            ],
        ];
        return $book;
    }
}
