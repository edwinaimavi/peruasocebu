<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'price',
        'type',
        'image',
        'status',
    ];

    // ============================
    // RELACIONES
    // ============================
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ============================
    // SCOPES
    // ============================
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }


    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
