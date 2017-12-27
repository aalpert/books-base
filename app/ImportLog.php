<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'isbn',
        'title',
        'author',
        'sku',
        'price',
        'import_id',
        'publisher',
        'status',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
    //
}
