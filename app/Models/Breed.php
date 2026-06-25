<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Breed extends Model
{
    protected $fillable = [
        'name', 'code', 'description', 'origin_country', 'characteristics',
        'status',
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
