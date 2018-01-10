<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{

    protected $fillable = array(
        'series_id',
        'title',
        'description',
        'sku',
        'image',
        'availability',
        'isbn',
        'format',
        'bookbinding',
        'year',
        'pages',
        'additional_notes',
    );

    /**
     * Establish one-two-many relationships with Source
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function publishers()
    {
        return $this->belongsToMany(Publisher::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function prices()
    {
        return $this->hasMany(BookPrice::class);
    }

    /**
     * Pre-builds the model fields
     * TODO: Move image to the Gallery?
     * @param $params
     * @return $this
     */
    public function prepare(array $params)
    {
        // Defaults
        $def = [
            'title' => '',
            'isbn' => '',
            'description' => '',
            'format' => '',
            'year' => 0,
            'pages' => 0,
            'image' => '',
            'additional_notes' => '',
            'sku' => '',
            'availability' => 'A',
            'bookbinding' => null,
        ];
        $params = array_merge($def, $params);

        // Getting raw fields
        $this->title = substr(trim($params['title']), 0, 255);
        $this->isbn = trim($params['isbn']);
        $this->description = $params['description'];
        $this->format = $params['format'];
        $this->year = $params['year'];
        $this->pages = $params['pages'];
        $this->image = $params['image'];
        $this->availability = $params['availability'];
        $this->additional_notes = $params['additional_notes'];
        $this->bookbinding = $params['bookbinding'];

        // Making SKU. simply taking first ISBN and removing the characters
        $this->sku = $params['sku'] ? $params['sku'] : static::skuFromIsbn($params['isbn']);

        // processing Publisher
//        $publisher = Publisher::firstOrCreate(['title' => $params['publisher']]);
//        $this->publisher_id = $publisher->id;

        // processing Series
        if (!empty($params['series'])) {
            $series = Series::firstOrCreate([
                'title' => $params['series']
            ]);
            $this->series_id = $series->id;
        }

        return $this;
    }

    /**
     * Attach author and category to the book
     * @param array $params
     * @return $this
     */
    public function attach(array $params)
    {
        // Processing Author
        if (!empty($this->id)) {
            $this->authors()->detach();
        }
        if (!empty($params['author'])) {
            $chunks = explode(',', $params['author']);
            foreach ($chunks as $chunk) {
                $author = Author::firstOrCreate(['name' => trim($chunk)]);
                $this->authors()->attach($author);
            }
        }

        // Processing Category
        if (!empty($this->id)) {
            $this->categories()->detach();
        }
        if (!empty($params['category'])) {
            $chunks = explode('||', $params['category']);
            foreach ($chunks as $chunk) {
                $category = Category::firstOrCreate(['title' => trim($chunk)]);
                $this->categories()->attach($category);
            }
        }

        // Processing Publisher
        if (!empty($this->id)) {
            $this->publishers()->detach();
        }
        if (!empty($params['publisher'])) {
            $chunks = explode('||', $params['publisher']);
            foreach ($chunks as $chunk) {
                $publisher = Publisher::firstOrCreate(['title' => trim($chunk)]);
                $this->publishers()->attach($publisher);
            }
        }

        return $this;
    }


    /**
     * Generates SKU from isbn or set of isbn's
     * @param string $isbn
     * @return null|string|string[]
     */
    public static function skuFromIsbn(string $isbn)
    {
        $isbn = explode(',', $isbn);
        // if there are many isbns, take last one
        $isbn = $isbn[count($isbn) - 1];
        $sku = preg_replace('/[^0-9]/', '', $isbn);
        return $sku;
    }


//    private function audit($originals)
//    {
////        dd($originals, $this->source_id);
//        if ((float)$originals['price'] != (float)$this->price) {
//            $this->history()->create([
//                'type' => 'price',
//                'value' => $this->price,
//            ]);
//        }
//
//        if ($originals['availability'] != $this->availability) {
//            $this->history()->create([
//                'type' => 'availability',
//                'value' => $this->availability,
//            ]);
//        }
//        if ($originals['source_id'] != $this->source_id) {
//            $this->history()->create([
//                'type' => 'source',
//                'value' => Source::where('id', $this->source_id)->pluck('title')->first(),
//            ]);
//        }
//        return $this;
//    }

    public function delete()
    {
        if (!empty($this->image)) {
            Storage::delete('images/covers/' . $this->image);
        }
        return parent::delete();
    }

//    public function save(array $options = [])
//    {
//        // Let's control availability once the book is updated
//        parent::save($options);
//        return $this;
//    }

    /**
     * Mark book as unavailable
     * @return $this
     */
    public function makeUnavailable()
    {
        return $this->setAvailability('NVN');
    }

    /**
     * Mark book as available
     * @return $this
     */
    public function makeAvailable()
    {
        return $this->setAvailability('A');
    }


    /**
     * Change availability
     * @param $state
     * @return $this
     */
    public function setAvailability($state)
    {
//        $this->availability = $state;
        $this->update(['availability' => $state]);
        Log::info('Setting availability '. $state);
        return $this;
    }

    /**
     * Check if book is available
     * @return bool
     */
    public function available()
    {
        return $this->availability !== 'NVN';
    }

    public function checkAvailability()
    {
        $this->fresh(['prices']);
        switch ($this->availability) {
            case 'A':
                // So the book is available. Let's check that it has prices. If not, make it unavailable
                if ($this->prices()->count() < 1) {
                    $this->makeUnavailable();
                }
                break;
            default:
                // In any other case let's make it available if there are prices
                if ($this->prices()->count() > 0 && $this->availability != 'A') {
                    $this->makeAvailable();
                }
                break;
        }
    }


    /**
     * Helper function to build path for image
     * @param $book
     * @return string
     */
    public static function imagePathFromRaw($book)
    {
        return substr(strtolower(str_slug($book['publisher'])) . '/book/' . str_slug($book['title']) . '/' . str_slug($book['title']) . '-' . str_random(5) . '-' . time(), 0, 245);
    }

    /**
     * Scoped filtering. should match naming convention
     * @param $query
     * @param $filters
     * @return mixed
     */
    public function scopeFilter($query, $filters)
    {
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['availability'])) {
            $query->where('availability', $filters['availability']);
        }
        if (!empty($filters['isbn'])) {
            $query->where('isbn', 'like', '%' . $filters['isbn'] . '%');
        }
        return $query;
    }

    /**
     * Add or remove prices. Very straight forward
     * @param array $prices
     * @return $this
     */
    public function updatePrices(array $prices)
    {
        foreach ($prices as $source => $price) {
            $price = BookPrice::format($price);
            if ($bp = $this->prices()->where('source_id', $source)->first()) {
                if (!empty($price) && $price > 0) {
                    $bp->update(['price' => $price]);
                } else {
                    $bp->delete();
                }
            } elseif (!empty($price) && $price > 0) {
                $this->prices()->create([
                    'source_id' => $source,
                    'price' => $price,
                ]);
            }
        }
        $this->checkAvailability();
        return $this;
    }

    /**
     * Find all books that have price with the source
     * @param $query
     * @param $source_id
     * @return mixed
     */
    public static function scopeWithSource($query, $source_id)
    {
        $query->join('book_prices', 'books.id', '=', 'book_prices.book_id')->where('book_prices.source_id', $source_id);
        return $query;
    }

}
