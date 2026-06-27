<?php

namespace App\Models;

use App\Models\Concerns\DecodesTextValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CattleGenealogyLink extends Model
{
    use DecodesTextValues;

    protected $fillable = [
        'cattle_id', 'relative_cattle_id', 'relation_type', 'generation_level',
        'relative_code', 'relative_name', 'breed_id', 'purity_percentage', 'notes',
    ];

    protected array $decodedTextAttributes = [
        'relative_code', 'relative_name', 'notes',
    ];

    protected function casts(): array
    {
        return ['purity_percentage' => 'decimal:2'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }

    public function relativeCattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class, 'relative_cattle_id');
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }
}
