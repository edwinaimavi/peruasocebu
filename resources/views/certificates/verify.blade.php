@extends('public.layouts.app')

@section('title', 'Verificacion de Certificado | PERU ASOCEBU')
@section('meta_description', 'Consulta publica de autenticidad documental, trazabilidad y respaldo ganadero de PERU ASOCEBU.')
@section('main_class', 'certificate-verify-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
@endpush

@section('content')
    @php
        $statusConfig = [
            'issued' => [
                'class' => 'verify-status-issued',
                'label' => 'Certificado emitido',
                'message' => 'El certificado se encuentra vigente y registrado en la plataforma.',
                'icon' => 'fas fa-check-circle',
            ],
            'cancelled' => [
                'class' => 'verify-status-cancelled',
                'label' => 'Certificado anulado',
                'message' => 'Este certificado fue anulado y no debe considerarse valido.',
                'icon' => 'fas fa-times-circle',
            ],
            'expired' => [
                'class' => 'verify-status-expired',
                'label' => 'Certificado vencido',
                'message' => 'Este certificado se encuentra vencido.',
                'icon' => 'fas fa-clock',
            ],
        ];

        $currentStatus = $certificate ? ($statusConfig[$certificate->status] ?? $statusConfig['expired']) : null;
        $owner = $certificate?->owner;
        $ranch = $certificate?->ranch;
        $cattle = $certificate?->cattle;
        $veterinarian = $certificate?->veterinarian;
        $ownerName = $owner?->owner_type === 'company' && $owner?->business_name ? $owner->business_name : $owner?->full_name;
        $ranchName = $ranch?->business_name ?: $ranch?->name;
        $cattleOwnerName = $cattle?->currentOwner?->owner_type === 'company' && $cattle?->currentOwner?->business_name
            ? $cattle->currentOwner->business_name
            : $cattle?->currentOwner?->full_name;
        $cattlePhoto = $cattle?->main_photo_path ? \Illuminate\Support\Facades\Storage::url($cattle->main_photo_path) : null;
        $qrUrl = $certificate?->qr_code_path ? \Illuminate\Support\Facades\Storage::url($certificate->qr_code_path) : null;
        $pdfUrl = $certificate?->pdf_path ? \Illuminate\Support\Facades\Storage::url($certificate->pdf_path) : null;
        $veterinarianSignatureUrl = $veterinarian?->signature_path ? \Illuminate\Support\Facades\Storage::url($veterinarian->signature_path) : null;
        $cattleRanchName = $cattle?->ranch?->business_name ?: $cattle?->ranch?->name;
        $sexLabel = match ($cattle?->sex) {
            'male' => 'Macho',
            'female' => 'Hembra',
            default => 'No registrado',
        };
    @endphp

    <section class="verify-container">
        <div class="verify-hero">
            <div class="verify-hero-copy">
                <span class="verify-eyebrow">PERU ASOCEBU</span>
                <h1>Verificacion de certificado</h1>
                <p>Consulta publica de autenticidad documental, trazabilidad y respaldo ganadero.</p>
            </div>

            <div class="verify-seal" aria-hidden="true">
                <i class="fas fa-certificate"></i>
            </div>
        </div>

        @if (! $certificate)
            <div class="verify-empty-card">
                <div class="verify-empty-icon" aria-hidden="true">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <span class="verify-eyebrow">Consulta sin resultado</span>
                <h2>Certificado no encontrado</h2>
                <p>No se encontro ningun certificado con el codigo ingresado.</p>
                <div class="verify-actions-row">
                    <a class="verify-action-btn" href="{{ route('public.home') }}">Volver al inicio</a>
                    <a class="verify-action-btn secondary" href="{{ route('public.home') }}#consulta">Realizar otra consulta</a>
                </div>
            </div>
        @else
            <div class="verify-status-card {{ $currentStatus['class'] }}">
                <div>
                    <span>{{ $currentStatus['label'] }}</span>
                    <p>{{ $currentStatus['message'] }}</p>
                </div>
                <i class="{{ $currentStatus['icon'] }}" aria-hidden="true"></i>
            </div>

            <div class="verify-layout">
                <div class="verify-main-card">
                    <section class="verify-section">
                        <h2 class="verify-section-title">
                            <i class="fas fa-file-contract" aria-hidden="true"></i>
                            Datos del certificado
                        </h2>
                        <div class="verify-grid">
                            <div class="verify-field"><small>Nro. certificado</small><strong>{{ $certificate->certificate_number ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Codigo de verificacion</small><strong>{{ $certificate->verification_code ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Tipo</small><strong>{{ $typeLabel ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Estado</small><strong>{{ $statusLabel ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Fecha de emision</small><strong>{{ $certificate->issue_date?->format('d/m/Y') ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Pureza</small><strong>{{ $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : 'No registrado' }}</strong></div>
                        </div>
                    </section>

                    <section class="verify-section">
                        <h2 class="verify-section-title">
                            <i class="fas fa-paw" aria-hidden="true"></i>
                            Ganado certificado
                        </h2>
                        <div class="verify-cattle-card">
                            <div class="verify-cattle-photo">
                                @if ($cattlePhoto)
                                    <img src="{{ $cattlePhoto }}" alt="Foto de {{ $cattle?->name ?: $cattle?->code }}">
                                @else
                                    <i class="fas fa-paw" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="verify-grid verify-grid-two">
                                <div class="verify-field"><small>Codigo</small><strong>{{ $cattle?->code ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Nombre</small><strong>{{ $cattle?->name ?: 'Sin nombre' }}</strong></div>
                                <div class="verify-field"><small>Raza</small><strong>{{ $cattle?->breed?->name ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Sexo</small><strong>{{ $sexLabel }}</strong></div>
                                <div class="verify-field"><small>Fecha de nacimiento</small><strong>{{ $cattle?->birth_date?->format('d/m/Y') ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Criadero / Hacienda</small><strong>{{ $cattleRanchName ?: $ranchName ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Propietario actual</small><strong>{{ $cattleOwnerName ?: $ownerName ?: 'No registrado' }}</strong></div>
                            </div>
                        </div>
                    </section>

                    <section class="verify-section">
                        <h2 class="verify-section-title">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            Propietario
                        </h2>
                        <div class="verify-grid">
                            <div class="verify-field"><small>Nombre o razon social</small><strong>{{ $ownerName ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Documento</small><strong>{{ $owner?->document_number ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Telefono</small><strong>{{ $owner?->phone ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Correo</small><strong>{{ $owner?->email ?: 'No registrado' }}</strong></div>
                        </div>
                    </section>

                    <section class="verify-section">
                        <h2 class="verify-section-title">
                            <i class="fas fa-warehouse" aria-hidden="true"></i>
                            Criadero emisor
                        </h2>
                        <div class="verify-grid">
                            <div class="verify-field"><small>Nombre / razon social</small><strong>{{ $ranchName ?: 'No registrado' }}</strong></div>
                            <div class="verify-field"><small>Documento / RUC</small><strong>{{ $ranch?->document_number ?: 'No registrado' }}</strong></div>
                            <div class="verify-field verify-field-wide"><small>Direccion</small><strong>{{ $ranch?->address ?: 'No registrado' }}</strong></div>
                        </div>
                    </section>

                    <section class="verify-section">
                        <h2 class="verify-section-title">
                            <i class="fas fa-user-md" aria-hidden="true"></i>
                            Veterinario / certificador
                        </h2>
                        @if ($veterinarian)
                            <div class="verify-grid">
                                <div class="verify-field"><small>Nombre</small><strong>{{ $veterinarian->full_name ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Colegiatura</small><strong>{{ $veterinarian->license_number ?: 'No registrado' }}</strong></div>
                                <div class="verify-field"><small>Especialidad</small><strong>{{ $veterinarian->specialty ?: 'No registrado' }}</strong></div>
                                <div class="verify-field verify-field-wide">
                                    <small>Firma</small>
                                    @if ($veterinarianSignatureUrl)
                                        <img class="verify-signature" src="{{ $veterinarianSignatureUrl }}" alt="Firma de {{ $veterinarian->full_name }}">
                                    @else
                                        <strong>No registrado</strong>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="verify-note">Sin certificador asignado</div>
                        @endif
                    </section>
                </div>

                <aside class="verify-side-card">
                    <div class="verify-side-heading">
                        <span>Validacion digital</span>
                        <strong>{{ $certificate->verification_code }}</strong>
                    </div>

                    <div class="verify-qr-box">
                        @if ($qrUrl)
                            <img src="{{ $qrUrl }}" alt="QR de verificacion del certificado {{ $certificate->certificate_number }}">
                            <span>QR de verificacion</span>
                        @else
                            <div class="verify-qr-placeholder">
                                <i class="fas fa-qrcode" aria-hidden="true"></i>
                                <span>QR no registrado</span>
                            </div>
                        @endif
                    </div>

                    @if ($pdfUrl)
                        <a class="verify-action-btn" href="{{ $pdfUrl }}" target="_blank" rel="noopener">
                            <i class="fas fa-file-pdf" aria-hidden="true"></i>
                            Ver certificado PDF
                        </a>
                    @endif
                    <a class="verify-action-btn secondary" href="{{ route('public.home') }}#consulta">Nueva consulta</a>
                    <a class="verify-action-btn ghost" href="{{ route('public.home') }}">Volver al inicio</a>

                    <div class="verify-side-note">
                        <strong>Documento verificable</strong>
                        <p>La informacion mostrada corresponde al registro publico asociado al codigo de verificacion.</p>
                    </div>
                </aside>
            </div>
        @endif
    </section>
@endsection
