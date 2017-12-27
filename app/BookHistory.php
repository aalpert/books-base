<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookHistory extends Model
{
    protected $fillable = ['price'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
