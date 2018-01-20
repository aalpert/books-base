<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    public function bookPrices()
    {
        return $this->hasMany(BookPrice::class);
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }
}
