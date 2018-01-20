<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends \Baum\Node
{
    protected $fillable = ['title'];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }
}
