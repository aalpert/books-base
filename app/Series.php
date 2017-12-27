<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = ['publisher_id', 'title'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}
