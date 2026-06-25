<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateSignature extends Model
{
    protected $fillable = [
        'certificate_id', 'person_type', 'person_name', 'position',
        'signature_path', 'seal_path',
    ];

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }
}
