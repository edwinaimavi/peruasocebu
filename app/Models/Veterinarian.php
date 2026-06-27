<?php

namespace App\Models;

use App\Models\Concerns\DecodesTextValues;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Veterinarian extends Model
{
    use DecodesTextValues, SoftDeletes;

    protected $fillable = [
        'full_name', 'document_type', 'document_number', 'license_number',
        'specialty', 'phone', 'email', 'address', 'signature_path', 'notes',
        'status',
    ];

    protected array $decodedTextAttributes = [
        'full_name', 'specialty', 'address', 'notes',
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
