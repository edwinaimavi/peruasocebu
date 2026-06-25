<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccination extends Model
{
    protected $fillable = [
        'cattle_id', 'veterinarian_id', 'vaccine_name', 'dose', 'batch_number',
        'application_date', 'next_due_date', 'observations',
    ];

    protected function casts(): array
    {
        return ['application_date' => 'date', 'next_due_date' => 'date'];
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
