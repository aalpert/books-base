<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BookPrice extends Model
{
    protected $fillable = array('source_id', 'book_id', 'price');
//    protected $primaryKey = ['source_id', 'book_id'];

    public function source() {
        return $this->belongsTo(Source::class);
    }

    public function book() {
        return $this->belongsTo(Book::class);
    }

    //

    /**
     * as long as Eloquent can't work with composite keys, we need to do manual delete
     * @return bool|null
     */
    public function delete() {
        return DB::table('book_prices')->where('source_id', $this->source_id)->where('book_id', $this->book_id)->delete();
    }

    /**
     * @param array $attributes
     * @param array $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        return DB::table('book_prices')->where('source_id', $this->source_id)->where('book_id', $this->book_id)->update($attributes);
    }

    /**
     * Helper function to preprocess price to the correct format
     * @param $price
     * @return mixed|string
     */
    public static function format($price)
    {
        $price = str_replace(',', '.', $price);
        $price = number_format((float)$price, 2, '.', '');
        return $price;
    }
}
