<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'image',
        'order'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }
}
