<?php

namespace App\Models;

use App\Models\Concerns\DecodesTextValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Breed extends Model
{
    use DecodesTextValues;

    protected $fillable = [
        'name', 'code', 'description', 'origin_country', 'characteristics',
        'image_path', 'status',
    ];

    protected array $decodedTextAttributes = [
        'name', 'description', 'origin_country', 'characteristics',
    ];

    public function cattle(): HasMany
    {
        return $this->hasMany(Cattle::class);
    }

    public function genealogyLinks(): HasMany
    {
        return $this->hasMany(CattleGenealogyLink::class);
    }
}
