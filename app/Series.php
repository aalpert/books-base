<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    protected $fillable = ['title'];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
