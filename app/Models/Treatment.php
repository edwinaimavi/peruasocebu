<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    protected $fillable = [
        'cattle_id', 'veterinarian_id', 'treatment_date', 'treatment_name',
        'medicine', 'dose', 'duration', 'reason', 'observations',
    ];

    protected function casts(): array
    {
        return ['treatment_date' => 'date'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);
    }
}
