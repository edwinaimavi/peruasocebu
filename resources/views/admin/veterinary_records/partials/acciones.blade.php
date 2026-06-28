<div class="d-flex justify-content-center align-items-center">
    @can('admin.veterinary-records.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewVeterinaryRecord mr-1" title="Ver detalle"
            data-id="{{ $record->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.veterinary-records.update')
        <button type="button" class="btn btn-outline-info btn-xs editVeterinaryRecord mr-1" title="Editar revision"
            data-id="{{ $record->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.veterinary-records.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteVeterinaryRecord" title="Eliminar revision"
            data-id="{{ $record->id }}"
            data-name="{{ $record->cattle?->code }} - {{ $record->record_date?->format('d/m/Y') }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
