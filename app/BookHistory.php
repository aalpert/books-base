<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookHistory extends Model
{
    protected $fillable = ['type', 'value'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
