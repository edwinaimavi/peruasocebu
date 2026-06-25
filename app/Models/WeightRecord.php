<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightRecord extends Model
{
    protected $fillable = [
        'cattle_id', 'weight_kg', 'record_date', 'body_condition', 'observations',
    ];

    protected function casts(): array
    {
        return ['weight_kg' => 'decimal:2', 'record_date' => 'date'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }
}
