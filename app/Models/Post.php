<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'status',
        'user_id',
        'category_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================
    // RELACIONES
    // ============================
    public function user()
    {
        return $this->belongsTo(User::class);
    }
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
