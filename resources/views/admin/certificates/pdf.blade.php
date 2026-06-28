<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }}</title>
    <style>
        body { color: #24342b; font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; }
        .page { padding: 32px; position: relative; }
        .header { border-bottom: 3px solid #1f4d36; padding-bottom: 16px; }
        .brand { color: #1f4d36; font-size: 24px; font-weight: 800; letter-spacing: .5px; }
        .subtitle { color: #8a6b25; font-size: 13px; margin-top: 4px; text-transform: uppercase; }
        .number { background: #f4f7f5; border: 1px solid #dfe9e3; border-radius: 8px; padding: 12px; text-align: right; }
        .grid { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; }
        .w60 { width: 60%; }
        .w40 { width: 40%; }
        .section { border: 1px solid #e3ebe7; border-radius: 8px; margin-top: 16px; padding: 14px; }
        .section-title { color: #1f4d36; font-size: 13px; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; }
        .label { color: #6f7d74; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .value { font-size: 13px; margin-bottom: 8px; }
        .badge { border-radius: 14px; color: #fff; display: inline-block; font-size: 11px; font-weight: 700; padding: 5px 10px; }
        .badge-green { background: #198754; }
        .badge-red { background: #dc3545; }
        .badge-gray { background: #6c757d; }
        .qr { border: 1px solid #e3ebe7; border-radius: 8px; height: 118px; padding: 6px; width: 118px; }
        .footer { border-top: 1px solid #e3ebe7; color: #68776d; font-size: 10px; margin-top: 20px; padding-top: 12px; text-align: center; }
        .cancelled { color: rgba(220, 53, 69, .18); font-size: 54px; font-weight: 900; left: 40px; position: absolute; top: 370px; transform: rotate(-18deg); }
        .signature-box { border-top: 1px solid #98aaa0; margin-top: 28px; padding-top: 8px; text-align: center; }
        .signature-img { height: 54px; max-width: 160px; object-fit: contain; }
        .seal-img { height: 54px; max-width: 90px; object-fit: contain; }
        table { border-collapse: collapse; width: 100%; }
        td { padding: 4px 8px 4px 0; vertical-align: top; width: 50%; }
    </style>
</head>
<body>
    <div class="page">
        @if ($certificate->status === 'cancelled')
            <div class="cancelled">CERTIFICADO ANULADO</div>
        @endif

        <div class="grid header">
            <div class="col w60">
                <div class="brand">{{ $certificate->ranch?->business_name ?: ($certificate->ranch?->name ?: 'PERU ASOCEBU') }}</div>
                <div class="subtitle">Certificado bovino de {{ $typeLabel }}</div>
                <div style="margin-top: 8px;">{{ $certificate->ranch?->address }}</div>
            </div>
            <div class="col w40 number">
                <div class="label">Nro. certificado</div>
                <div class="value"><strong>{{ $certificate->certificate_number }}</strong></div>
                <div class="label">Codigo verificacion</div>
                <div class="value">{{ $certificate->verification_code }}</div>
            </div>
        </div>

        <div class="grid section">
            <div class="col w60">
                <div class="section-title">Datos del certificado</div>
                <table>
                    <tr><td><div class="label">Tipo</div><div class="value">{{ $typeLabel }}</div></td><td><div class="label">Estado</div><div class="value">{{ $statusLabel }}</div></td></tr>
                    <tr><td><div class="label">Fecha de emision</div><div class="value">{{ $certificate->issue_date?->format('d/m/Y') }}</div></td><td><div class="label">Pureza certificada</div><div class="value">{{ $certificate->purity_percentage !== null ? number_format((float) $certificate->purity_percentage, 2).'%' : '-' }}</div></td></tr>
                </table>
                <span class="badge {{ $certificate->status === 'issued' ? 'badge-green' : ($certificate->status === 'cancelled' ? 'badge-red' : 'badge-gray') }}">{{ $statusLabel }}</span>
            </div>
            <div class="col w40" style="text-align: right;">
                @if ($qrPath)
                    <img class="qr" src="{{ $qrPath }}" alt="QR">
                @endif
                <div class="label" style="margin-top: 6px;">Verificacion publica</div>
                <div style="font-size: 9px;">{{ $verifyUrl }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Datos del ganado</div>
            <table>
                <tr><td><div class="label">Codigo</div><div class="value">{{ $certificate->cattle?->code ?: '-' }}</div></td><td><div class="label">Nombre</div><div class="value">{{ $certificate->cattle?->name ?: '-' }}</div></td></tr>
                <tr><td><div class="label">Raza</div><div class="value">{{ $certificate->cattle?->breed?->name ?: '-' }}</div></td><td><div class="label">Sexo</div><div class="value">{{ $certificate->cattle?->sex === 'male' ? 'Macho' : ($certificate->cattle?->sex === 'female' ? 'Hembra' : '-') }}</div></td></tr>
                <tr><td><div class="label">Fecha de nacimiento</div><div class="value">{{ $certificate->cattle?->birth_date?->format('d/m/Y') ?: '-' }}</div></td><td><div class="label">Pureza registrada</div><div class="value">{{ $certificate->cattle?->purity_percentage !== null ? number_format((float) $certificate->cattle->purity_percentage, 2).'%' : '-' }}</div></td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Propietario y certificador</div>
            <table>
                <tr><td><div class="label">Propietario</div><div class="value">{{ $certificate->owner?->business_name ?: ($certificate->owner?->full_name ?: '-') }}</div></td><td><div class="label">Documento</div><div class="value">{{ $certificate->owner?->document_number ?: '-' }}</div></td></tr>
                <tr><td><div class="label">Veterinario / certificador</div><div class="value">{{ $certificate->veterinarian?->full_name ?: '-' }}</div></td><td><div class="label">Colegiatura</div><div class="value">{{ $certificate->veterinarian?->license_number ?: '-' }}</div></td></tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Observaciones</div>
            <div>{{ $certificate->observations ?: 'Sin observaciones.' }}</div>
        </div>

        <div class="section">
            <div class="section-title">Firmas y sellos</div>
            @if ($certificate->signatures->isEmpty())
                <div class="signature-box">
                    <div class="value">Firma pendiente</div>
                </div>
            @else
                @php
                    $personTypes = [
                        'owner' => 'Dueno',
                        'veterinarian' => 'Veterinario',
                        'representative' => 'Representante',
                        'certifier' => 'Certificador',
                        'other' => 'Otro',
                    ];
                @endphp
                <table>
                    @foreach ($certificate->signatures->chunk(2) as $row)
                        <tr>
                            @foreach ($row as $signature)
                                @php
                                    $signaturePath = $signature->signature_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($signature->signature_path)
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->path($signature->signature_path)
                                        : null;
                                    $sealPath = $signature->seal_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($signature->seal_path)
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->path($signature->seal_path)
                                        : null;
                                @endphp
                                <td>
                                    <div class="signature-box">
                                        @if ($signaturePath)
                                            <img class="signature-img" src="{{ $signaturePath }}" alt="Firma">
                                        @else
                                            <div style="height: 54px; color: #8a958f;">Firma pendiente</div>
                                        @endif
                                        @if ($sealPath)
                                            <div><img class="seal-img" src="{{ $sealPath }}" alt="Sello"></div>
                                        @endif
                                        <div class="value"><strong>{{ $signature->person_name }}</strong></div>
                                        <div>{{ $signature->position ?: $personTypes[$signature->person_type] ?? 'Firmante' }}</div>
                                        <div class="label">{{ $personTypes[$signature->person_type] ?? 'Otro' }}</div>
                                    </div>
                                </td>
                            @endforeach
                            @if ($row->count() === 1)
                                <td></td>
                            @endif
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <div class="footer">
            Este certificado puede verificarse escaneando el codigo QR o ingresando el codigo de verificacion en la plataforma.
        </div>
    </div>
</body>
</html>
