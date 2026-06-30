@extends('public.layouts.app')

@section('title', 'Resultado de busqueda | PERU ASOCEBU')
@section('body_class', 'public-result-page')
@section('main_class', 'result-wrap')

@section('content')
    @php
        $typeLabel = match ($type) {
            'cattle_code' => 'Codigo de ganado',
            'certificate_number' => 'Numero de certificado',
            'verification_code' => 'Codigo de verificacion',
            default => 'Consulta',
        };
    @endphp

    <section class="result-hero compact">
        <div>
            <span class="eyebrow eyebrow-light"><span></span>Consulta publica</span>
            <h1>No encontramos resultados para tu busqueda.</h1>
            <p>{{ $message }}</p>
        </div>
    </section>

    <section class="result-card not-found-card">
        <div class="result-data-grid">
            <article><span>Termino buscado</span><strong>{{ $query }}</strong></article>
            <article><span>Tipo de busqueda</span><strong>{{ $typeLabel }}</strong></article>
        </div>

        <div class="result-actions">
            <a class="btn btn-primary" href="{{ route('public.home') }}">Volver al inicio</a>
            <a class="btn btn-gold" href="{{ route('public.home') }}#consulta">Intentar nuevamente</a>
        </div>
    </section>
@endsection
