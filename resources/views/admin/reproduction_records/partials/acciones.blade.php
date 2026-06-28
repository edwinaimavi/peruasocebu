<div class="d-flex justify-content-center align-items-center">
    @can('admin.reproduction-records.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewReproductionRecord mr-1" title="Ver detalle"
            data-id="{{ $record->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.reproduction-records.update')
        <button type="button" class="btn btn-outline-info btn-xs editReproductionRecord mr-1" title="Editar registro"
            data-id="{{ $record->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.reproduction-records.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteReproductionRecord" title="Eliminar registro"
            data-id="{{ $record->id }}"
            data-name="{{ $record->cattle?->code }} - {{ $record->reproduction_date?->format('d/m/Y') }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
