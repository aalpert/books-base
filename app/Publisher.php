<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    protected $fillable = ['title', 'description'];

//    public function series()
//    {
//        return $this->hasMany(Series::class);
//    }

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
