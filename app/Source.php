<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }
}
