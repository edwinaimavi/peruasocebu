<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ranch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'business_name', 'document_type', 'document_number', 'address',
        'department', 'province', 'district', 'phone', 'email',
        'representative_name', 'description', 'logo_path', 'seal_path',
        'signature_path', 'status',
    ];

    public function cattle(): HasMany
    {
        return $this->hasMany(Cattle::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
