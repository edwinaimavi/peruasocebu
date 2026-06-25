<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinarian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'full_name', 'document_type', 'document_number', 'license_number',
        'specialty', 'phone', 'email', 'address', 'signature_path', 'notes',
        'status',
    ];

    public function veterinaryRecords(): HasMany
    {
        return $this->hasMany(VeterinaryRecord::class);
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(Vaccination::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
