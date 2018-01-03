<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{

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

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function history()
    {
        return $this->hasMany(BookHistory::class);
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
            'price' => 0,
            'description' => '',
            'format' => '',
            'year' => 0,
            'pages' => 0,
            'source_id' => 0,
            'image' => '',
            'additional_notes' => '',
            'sku' => '',
            'availability' => 'A',
        ];
        $params = array_merge($def, $params);

        // Getting raw fields
        $this->title = substr(trim($params['title']), 0, 255);
        $this->isbn = trim($params['isbn']);
        $this->price = (number_format((float)$params['price'], 2, '.', ''));
        $this->description = $params['description'];
        $this->format = $params['format'];
        $this->year = $params['year'];
        $this->pages = $params['pages'];
        $this->source_id = $params['source'];
        $this->image = $params['image'];
        $this->availability = $params['availability'];
        $this->additional_notes = $params['additional_notes'];

        // Making SKU. simply taking first ISBN and removing the characters
        $this->sku = $params['sku'] ? $params['sku'] : static::skuFromIsbn($params['isbn']);

        // processing Publisher
        $publisher = Publisher::firstOrCreate(['title' => $params['publisher']]);
        $this->publisher_id = $publisher->id;

        // processing Series
        if (!empty($params['series'])) {
            $series = Series::firstOrCreate([
                'title' => $params['series'],
                'publisher_id' => $publisher->id,
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
        return $this;
    }


    /**
     * Generates SKU from isbn or set of isbn's
     * @param string $isbn
     * @return null|string|string[]
     */
    public static function skuFromIsbn(string $isbn)
    {
        $isbn = explode(',', $isbn)[0];
        $sku = preg_replace('/[^0-9]/', '', $isbn);
        return $sku;
    }


    private function audit($originals)
    {
//        dd($originals, $this->source_id);
        if ((float)$originals['price'] != (float)$this->price) {
            $this->history()->create([
                'type' => 'price',
                'value' => $this->price,
            ]);
        }

        if ($originals['availability'] != $this->availability) {
            $this->history()->create([
                'type' => 'availability',
                'value' => $this->availability,
            ]);
        }
        if ($originals['source_id'] != $this->source_id) {
            $this->history()->create([
                'type' => 'source',
                'value' => Source::where('id', $this->source_id)->pluck('title')->first(),
            ]);
        }
        return $this;
    }

    public function delete()
    {
        if (!empty($this->image)) {
            Storage::delete('images/covers/' . $this->image);
        }
        return parent::delete();
    }

    public function save(array $options = [])
    {
        $originals = [
            'price' => $this->getOriginal('price'),
            'availability' => $this->getOriginal('availability'),
            'source_id' => $this->getOriginal('source_id'),
        ];
        parent::save($options);
        $this->audit($originals);
        return $this;
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
        $this->availability = $state;
        $this->update();
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
}
