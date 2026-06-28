<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certificate_number', 'cattle_id', 'ranch_id', 'owner_id',
        'veterinarian_id', 'issue_date', 'purity_percentage', 'certificate_type',
        'verification_code', 'qr_code_path', 'pdf_path', 'observations', 'status',
    ];

    protected function casts(): array
    {
        return ['issue_date' => 'date', 'purity_percentage' => 'decimal:2'];
    }

    public function cattle(): BelongsTo
    {
        return $this->belongsTo(Cattle::class);
    }

    public function ranch(): BelongsTo
    {
        return $this->belongsTo(Ranch::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function veterinarian(): BelongsTo
    {
        return $this->belongsTo(Veterinarian::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(CertificateSignature::class)->latest('id');
    }
}
