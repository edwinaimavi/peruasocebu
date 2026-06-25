<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReproductionRecord extends Model
{
    protected $fillable = [
        'cattle_id', 'partner_cattle_id', 'method', 'reproduction_date',
        'pregnancy_check_date', 'pregnancy_result', 'birth_date',
        'offspring_cattle_id', 'observations',
    ];

    protected function casts(): array
    {
        return [
            'reproduction_date' => 'date',
            'pregnancy_check_date' => 'date',
            'birth_date' => 'date',
        ];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Cattle::class, 'partner_cattle_id');
    }

    public function offspring(): BelongsTo
    {
        return $this->belongsTo(Cattle::class, 'offspring_cattle_id');
    }
}
