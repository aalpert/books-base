<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Excel;
use Illuminate\Support\Facades\Log;

class Import extends Model
{
    protected $fillable = ['source_id', 'filename', 'limit_publishers'];

    public function start()
    {
        set_time_limit(0);
        $import = $this;
        $import->status = 'started';
        $import->update();
        $limitations = false;
        if (!empty($this->limit_publishers)) {
            $limitations = array_map(function ($a) {
                return mb_strtolower(trim($a));
            }, explode('||', $this->limit_publishers));
        }
        Excel::filter('chunk')->load($import->filename)->chunk(1000, function ($results) use ($import, $limitations) {
            $import = \App\Import::find($import->id);
            $source_title = Source::find($import->source_id)->pluck('title')->first();
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
                    $raw = \App\ImportBook::process($raw);
                    $raw['source'] = $import->source_id;
                    $book = new \App\Book;
                    $book->prepare($raw)->save();
                    $book->attach($raw);
                    $import->created++;
                    $import->addLog($book, 'created');
                }
                $import->update();
            }
        });
        $import = \App\Import::find($import->id);
        $import->status = 'finished';
        $import->update();
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function logs()
    {
        return $this->hasMany(ImportLog::class);
    }

    public function addLog($book, $status)
    {
        $this->logs()->create([
            'title' => mb_convert_encoding(substr($book->title, 0, 255), 'UTF-8', 'UTF-8'),
            'isbn' => $book->isbn,
            'price' => $book->price,
            'publisher' => $book->publisher['title'],
            'author' => mb_convert_encoding(substr(implode(', ', $book->authors()->pluck('name')->all()), 0, 255), 'UTF-8', 'UTF-8'),
            'sku' => $book->sku,
            'status' => $status,
        ]);
    }

    /**
     * Remove books that are in base but not in the price list
     */
    public function remove()
    {
        Log::info('Cleaning up...');
        if (!$this->shouldClean()) {
            Log::info('Cleanup is not needed');
            return $this;
        }
        $books = \App\Book::where('updated_at', '<', $this->created_at)->where('source_id', '=', $this->source_id);

        // Limit to the publishers that were processed in this run
        if (!empty($this->limit_publishers && $this->clear == 'publishers')) {
            $publishers = explode('||', $this->limit_publishers);
            if (count($publishers)) {
                $publishers = \App\Publisher::whereIn('title', $publishers);
                if ($publishers->count()) {
                    $books->whereIn('publisher_id', $publishers->pluck('id')->all());
                }
            }
        }

        $total = $books->count();
        Log::info(sprintf('Outdated books: %d', $total));
        if ($total) {
            $this->status = 'started';
            $this->update();
            $pn = 0;
            $pp = 100;
            while ($total > 0) {
                foreach ($books->skip($pn * $pp)->take($pp)->get()->all() as $book) {
                    $this->addLog($book, 'deleted');
                    $this->removed++;
//                    $book->delete();
                    $book->makeUnavailable();
                }
                $pn++;
                $total -= $pp;
            }
            $this->status = 'finished';
            $this->update();
        }
    }

    /**
     * Defines if the import needs to clear the database after run
     * @return bool
     */
    public function shouldClean()
    {
        return !empty($this->clear) && $this->clear != 'none';
    }
}
