<?php

namespace App;

use Carbon\Carbon;
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
        'params',
    );

    protected $casts = [
        'details' => 'array',
    ];


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
            'description' => '',
            'image' => '',
            'sku' => '',
            'availability' => 'A',
            'details' => []
        ];
        $params = array_merge($def, $params);

        // Getting raw fields
        $this->title = substr(trim($params['title']), 0, 255);
        $this->image = $params['image'];
        $this->description = $params['description'];
        $this->year = $params['year'];
        $this->availability = $params['availability'];

        $this->details = $params['details'];
//        $this->pages = $params['pages'];
//        $this->image = $params['image'];
//        $this->additional_notes = $params['additional_notes'];
//        $this->bookbinding = $params['bookbinding'];

        // Making SKU. simply taking first ISBN and removing the characters
        $this->sku = $params['sku'] ? $params['sku'] : static::skuFromIsbn($params['details']['isbn']);

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


    /**
     * Delete all additional data
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if (!empty($this->image)) {
            Storage::delete('images/covers/' . $this->image);
        }
        return parent::delete();
    }


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
        Log::info('Setting availability ' . $state);
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

    /**
     * Sets the correct availability flag
     */
    public function checkAvailability()
    {
        $this->fresh(['prices']);
        switch ($this->availability) {
            case 'A':
                // So the book is available. Let's check that it has prices. If not, make it unavailable
                if ($this->prices()->count() < 1) {
                    $this->makeUnavailable();
                } elseif (!$this->hasRealPrice()) {
                    $this->setAvailability('AN');
                }
                break;
            default:
                // In any other case let's make it available if there are prices
                if ($this->prices()->count() > 0) {
                    if ($this->hasRealPrice()) {
                        $this->makeAvailable();
                    }
                }
                break;
        }
    }

    public function hasRealPrice() {
        return $this->prices()->where('available_at', '<=', date('Y-m-d'))->count() > 0;
    }


    /**
     * Helper function to build path for image
     * @param $book
     * @return string
     */
    public static function imagePathFromRaw($book)
    {
        return substr(strtolower(str_slug($book['publisher'])) . '/' . str_slug($book['title']) . '/' . str_slug($book['title']) . '-' . str_random(5) . '-' . time(), 0, 245);
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
        foreach ($prices as $source => $val) {
            if (is_array($val)) {
                $price = BookPrice::format($val['price']);
                $avail = (isset($val['available_at']) && !is_null($val['available_at'])) ? $val['available_at'] : Carbon::now();
            } else {
                $price = BookPrice::format($val);
                $avail = Carbon::now();
            }
            if ($bp = $this->prices()->where('source_id', $source)->first()) {
                if (!empty($price) && $price > 0) {
                    $bp->update([
                        'price' => $price,
                        'available_at' => $avail,
                    ]);
                } else {
                    $bp->delete();
                }
            } elseif (!empty($price) && $price > 0) {
                $this->prices()->create([
                    'source_id' => $source,
                    'available_at' => $avail,
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
