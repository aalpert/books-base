<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'details',
        'status',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
    //
}
