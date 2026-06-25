<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryRecord extends Model
{
    protected $fillable = [
        'cattle_id', 'veterinarian_id', 'record_date', 'record_type', 'diagnosis',
        'treatment', 'observations', 'next_visit_date', 'document_path',
    ];

    protected function casts(): array
    {
        return ['record_date' => 'date', 'next_visit_date' => 'date'];
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
