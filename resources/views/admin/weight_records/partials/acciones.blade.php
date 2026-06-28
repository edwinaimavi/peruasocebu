<div class="d-flex justify-content-center align-items-center">
    @can('admin.weight-records.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewWeightRecord mr-1" title="Ver detalle"
            data-id="{{ $record->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.weight-records.update')
        <button type="button" class="btn btn-outline-info btn-xs editWeightRecord mr-1" title="Editar pesaje"
            data-id="{{ $record->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.weight-records.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteWeightRecord" title="Eliminar pesaje"
            data-id="{{ $record->id }}"
            data-name="{{ $record->cattle?->code }} - {{ $record->record_date?->format('d/m/Y') }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
