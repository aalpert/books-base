<?php

namespace App;

use App\Import\Galina;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class Import extends Model
{
    protected $fillable = ['source_id', 'params', 'limit_publishers'];


    protected $casts = [
        'params' => 'array',
    ];

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
            default:
                $this->status = 'failed';
        }
//        dd($this->params);

        $this->update();
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
