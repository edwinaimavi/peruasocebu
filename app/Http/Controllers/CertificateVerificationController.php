<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function show(?string $code = null): View
    {
        $certificate = $code
            ? Certificate::with(['cattle.breed', 'ranch', 'owner', 'veterinarian'])
                ->where('verification_code', $code)
                ->first()
            : null;

        return view('certificates.verify', [
            'certificate' => $certificate,
            'typeLabel' => $certificate ? $this->typeLabel($certificate->certificate_type) : null,
            'statusLabel' => $certificate ? $this->statusLabel($certificate->status) : null,
        ]);
    }

    private function typeLabel(?string $type): string
    {
        return match ($type) {
            'breed' => 'Raza',
            'genealogy' => 'Genealogia',
            'ownership' => 'Propiedad',
            'purity' => 'Pureza',
            default => '-',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'issued' => 'Emitido',
            'cancelled' => 'Anulado',
            'expired' => 'Vencido',
            default => '-',
        };
    }
}
