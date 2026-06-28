<div class="d-flex justify-content-center align-items-center">
    @can('admin.certificates.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewCertificate mr-1" title="Ver detalle"
            data-id="{{ $certificate->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.certificates.update')
        <button type="button" class="btn btn-outline-info btn-xs editCertificate mr-1" title="Editar certificado"
            data-id="{{ $certificate->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.certificates.index')
        <a class="btn btn-outline-danger btn-xs mr-1" title="Ver PDF"
            href="{{ route('admin.certificates.pdf', $certificate) }}" target="_blank" rel="noopener">
            <i class="fas fa-file-pdf"></i>
        </a>
    @endcan

    @can('admin.certificates.store')
        <button type="button" class="btn btn-outline-secondary btn-xs regenerateCertificatePdf mr-1" title="Regenerar PDF"
            data-id="{{ $certificate->id }}">
            <i class="fas fa-sync-alt"></i>
        </button>
    @endcan

    @can('admin.certificates.update')
        @if ($certificate->status !== 'cancelled')
            <button type="button" class="btn btn-outline-warning btn-xs cancelCertificate mr-1" title="Anular certificado"
                data-id="{{ $certificate->id }}" data-name="{{ $certificate->certificate_number }}">
                <i class="fas fa-ban"></i>
            </button>
        @endif
    @endcan

    @can('admin.certificates.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteCertificate" title="Eliminar certificado"
            data-id="{{ $certificate->id }}" data-name="{{ $certificate->certificate_number }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
