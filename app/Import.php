<?php

namespace App;

use App\Import\Booksnook;
use App\Import\Galina;
use App\Import\Ksd;
use App\Import\Mahkha;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class Import extends Model
{
    protected $fillable = ['source_id', 'params', 'limit_publishers'];


    protected $casts = [
        'params' => 'array',
    ];


    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function logs()
    {
        return $this->hasMany(ImportLog::class);
    }

    //

    /**
     * Start processing
     */
    public function start()
    {
        set_time_limit(0);
        $this->status = 'started';
        $this->update();

        switch ($this->source['driver']) {
            case 'galina':
                $importId = $this->id;
                Excel::filter('chunk')->load($this->params['filename'])->chunk(10, function ($results) use ($importId) {
                    Galina::process($results, $importId);
                });
                $this->fresh();
                $this->status = 'finished';
                break;

            case 'ksd':
                $importId = $this->id;
                Excel::filter('chunk')->load($this->params['filename'])->chunk(10, function ($results) use ($importId) {
                    Ksd::process($results, $importId);
                });
                $this->fresh();
                $this->status = 'finished';
                break;

            case 'mahkha':
                $importId = $this->id;
                Excel::filter('chunk')->load($this->params['filename'])->chunk(10, function ($results) use ($importId) {
                    Mahkha::process($results, $importId);
                });
                $this->fresh();
                $this->status = 'finished';
                break;

            case 'booksnook':
                Booksnook::process($this);
                $this->status = 'finished';
                break;

            default:
                $this->status = 'failed';
        }
//        dd($this->params);

        $this->update();
    }

    public function addLog($item, $status, $price = 0)
    {
        $this->logs()->create([
            'details' => [
                'id' => $item->id,
                'isbn' => $item->details['isbn'],
                'title' => $item->title,
                'price' => $price,
            ],
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
        $books = \App\Book::withSource($this->source_id)->where('books.updated_at', '<', $this->created_at);

        // Limit to the publishers that were processed in this run
        if (!empty($this->limit_publishers && $this->clear == 'publishers')) {
            $publishers = explode('||', $this->limit_publishers);
            if (count($publishers)) {
                $publishers = \App\Publisher::whereIn('title', $publishers);
                if ($publishers->count()) {
                    $books->publishers()->whereIn('id', $publishers->pluck('id')->all());
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
                    $book->prices()->where('source_id', $this->source_id)->delete();
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


    /**
     * update book while import
     * @param $book
     * @param $raw
     * @return mixed
     */
    public function updateBook($book, $raw)
    {
//        dd($raw);
        $book->touch();
        // processing price
        $bp = $book->prices()->where('source_id', $this->source_id)->first();
        $raw['price'] = BookPrice::format($raw['price']);
        $price_param = (isset($raw['available_at']) && !empty($raw['available_at'])) ? [
            'price' => $raw['price'],
            'available_at' => $raw['available_at'],
        ] : $raw['price'];
        if (!is_null($bp)) {
            // The book has this source, updating
            $book->updatePrices([$this->source_id => $price_param]);
            if ($bp->price != $raw['price']) {
                $this->updated++;
                $this->addLog($book, 'updated', $raw['price']);
            }
        } else {
            // Creating new price entry
            $book->updatePrices([$this->source_id => $price_param]);
            $this->appeared++;
            $this->addLog($book, 'appeared', $raw['price']);
        }
        $this->update();
        return $book;
    }
}
