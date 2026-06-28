<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificacion de certificado | PERU ASOCEBU</title>
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <style>
        body { background: #f3f7f4; color: #26372d; }
        .verify-wrap { margin: 0 auto; max-width: 980px; padding: 32px 16px; }
        .verify-header { background: #1f4d36; border-radius: 12px; color: #fff; padding: 28px; }
        .verify-card { border: 0; border-radius: 10px; box-shadow: 0 12px 30px rgba(31, 77, 54, .12); }
        .verify-label { color: #6c757d; font-size: .76rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
        .verify-value { font-size: 1rem; margin-bottom: 14px; word-break: break-word; }
    </style>
</head>
<body>
    <main class="verify-wrap">
        <section class="verify-header mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <div class="text-uppercase small font-weight-bold">PERU ASOCEBU</div>
                    <h1 class="h3 mb-1">Verificacion de certificado</h1>
                    <div>Consulta publica de autenticidad documental.</div>
                </div>
                <i class="fas fa-certificate fa-3x mt-3 mt-md-0"></i>
            </div>
        </section>

        @if (! $certificate)
            <div class="alert alert-danger verify-card">
                <h2 class="h5 mb-2">Certificado no encontrado</h2>
                <p class="mb-0">El codigo ingresado no corresponde a un certificado registrado.</p>
            </div>
        @else
            @if ($certificate->status === 'cancelled')
                <div class="alert alert-danger verify-card">
                    <strong>Certificado anulado.</strong> Este documento no debe considerarse vigente.
                </div>
            @elseif ($certificate->status === 'expired')
                <div class="alert alert-warning verify-card">
                    <strong>Certificado vencido.</strong> Revise la vigencia antes de usarlo.
                </div>
            @else
                <div class="alert alert-success verify-card">
                    <strong>Certificado emitido.</strong> El codigo fue encontrado en la plataforma.
                </div>
            @endif

            <div class="card verify-card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="verify-label">Nro. certificado</div>
                            <div class="verify-value font-weight-bold">{{ $certificate->certificate_number }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="verify-label">Codigo de verificacion</div>
                            <div class="verify-value">{{ $certificate->verification_code }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Estado</div>
                            <div class="verify-value">{{ $statusLabel }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Tipo</div>
                            <div class="verify-value">{{ $typeLabel }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Fecha emision</div>
                            <div class="verify-value">{{ $certificate->issue_date?->format('d/m/Y') ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="verify-label">Ganado certificado</div>
                            <div class="verify-value">{{ $certificate->cattle?->code }} - {{ $certificate->cattle?->name ?: 'Sin nombre' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="verify-label">Raza</div>
                            <div class="verify-value">{{ $certificate->cattle?->breed?->name ?: '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Pureza</div>
                            <div class="verify-value">{{ $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Criadero emisor</div>
                            <div class="verify-value">{{ $certificate->ranch?->business_name ?: ($certificate->ranch?->name ?: '-') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="verify-label">Propietario</div>
                            <div class="verify-value">{{ $certificate->owner?->business_name ?: ($certificate->owner?->full_name ?: '-') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
