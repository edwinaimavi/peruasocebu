@extends('public.layouts.app')

@section('title', 'Consulta de certificado | PERU ASOCEBU')
@section('body_class', 'public-result-page')
@section('main_class', 'result-wrap')

@section('content')
    @php
        $statusLabel = match ($certificate->status) {
            'issued' => 'Emitido',
            'cancelled' => 'Anulado',
            'expired' => 'Vencido',
            default => '-',
        };
        $typeLabel = match ($certificate->certificate_type) {
            'breed' => 'Raza',
            'genealogy' => 'Genealogia',
            'ownership' => 'Propiedad',
            'purity' => 'Pureza',
            default => '-',
        };
        $ownerName = $certificate->owner?->owner_type === 'company' && $certificate->owner?->business_name
            ? $certificate->owner->business_name
            : $certificate->owner?->full_name;
    @endphp

    <a class="result-back" href="{{ route('public.home') }}#consulta">Volver al inicio</a>

        <section class="result-hero">
            <div>
                <span class="eyebrow eyebrow-light"><span></span>Validacion publica</span>
                <h1>Certificado {{ $statusLabel }}</h1>
                <p>Resultado encontrado para <strong>{{ $query }}</strong>.</p>
            </div>
            <div class="result-status status-{{ $certificate->status }}">{{ $statusLabel }}</div>
        </section>

        @if ($certificate->status === 'cancelled')
            <div class="result-alert danger">Este certificado se encuentra anulado.</div>
        @elseif ($certificate->status === 'expired')
            <div class="result-alert warning">Este certificado se encuentra vencido.</div>
        @endif

        <section class="result-card result-main-grid">
            <div class="result-seal">
                @if ($certificate->qr_code_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($certificate->qr_code_path) }}" alt="QR del certificado {{ $certificate->certificate_number }}">
                @else
                    <svg viewBox="0 0 180 180" aria-hidden="true"><rect x="24" y="24" width="48" height="48"/><rect x="108" y="24" width="48" height="48"/><rect x="24" y="108" width="48" height="48"/><path d="M108 108h18v18h-18zM138 108h18v48h-18zM108 138h18v18h-18z"/></svg>
                @endif
                <span>Codigo de verificacion</span>
                <strong>{{ $certificate->verification_code }}</strong>
            </div>

            <div class="result-details">
                <div class="result-data-grid">
                    <article><span>Nro. certificado</span><strong>{{ $certificate->certificate_number }}</strong></article>
                    <article><span>Estado</span><strong>{{ $statusLabel }}</strong></article>
                    <article><span>Tipo</span><strong>{{ $typeLabel }}</strong></article>
                    <article><span>Fecha de emision</span><strong>{{ $certificate->issue_date?->format('d/m/Y') ?: '-' }}</strong></article>
                    <article><span>Ganado certificado</span><strong>{{ $certificate->cattle ? trim($certificate->cattle->code.' - '.($certificate->cattle->name ?: 'Sin nombre')) : '-' }}</strong></article>
                    <article><span>Raza</span><strong>{{ $certificate->cattle?->breed?->name ?: '-' }}</strong></article>
                    <article><span>Criadero</span><strong>{{ $certificate->ranch?->business_name ?: ($certificate->ranch?->name ?: '-') }}</strong></article>
                    <article><span>Propietario</span><strong>{{ $ownerName ?: '-' }}</strong></article>
                    <article><span>Veterinario / certificador</span><strong>{{ $certificate->veterinarian?->full_name ?: '-' }}</strong></article>
                    <article><span>Pureza</span><strong>{{ $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : '-' }}</strong></article>
                </div>

                <div class="result-actions">
                    <a class="btn btn-primary" href="{{ route('certificates.verify', $certificate->verification_code) }}">Ver validacion</a>
                    @if ($certificate->pdf_path)
                        <a class="btn btn-gold" href="{{ \Illuminate\Support\Facades\Storage::url($certificate->pdf_path) }}" target="_blank" rel="noopener">Ver PDF</a>
                    @endif
                </div>
            </div>
        </section>
@endsection
