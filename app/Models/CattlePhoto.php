<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CattlePhoto extends Model
{
    protected $fillable = [
        'cattle_id', 'image_path', 'title', 'description', 'is_main', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_main' => 'boolean'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }
}
