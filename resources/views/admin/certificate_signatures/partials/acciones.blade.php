<div class="d-flex justify-content-center align-items-center">
    @can('admin.certificate-signatures.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewCertificateSignature mr-1" title="Ver detalle"
            data-id="{{ $signature->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.certificate-signatures.update')
        <button type="button" class="btn btn-outline-info btn-xs editCertificateSignature mr-1" title="Editar firma"
            data-id="{{ $signature->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.certificate-signatures.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteCertificateSignature" title="Eliminar firma"
            data-id="{{ $signature->id }}" data-name="{{ $signature->person_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
