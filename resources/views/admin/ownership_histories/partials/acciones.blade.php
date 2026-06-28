<div class="d-flex justify-content-center align-items-center">
    @can('admin.ownership-histories.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewOwnershipHistory mr-1" title="Ver detalle"
            data-id="{{ $history->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.ownership-histories.update')
        <button type="button" class="btn btn-outline-info btn-xs editOwnershipHistory mr-1" title="Editar historial"
            data-id="{{ $history->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.ownership-histories.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteOwnershipHistory" title="Eliminar historial"
            data-id="{{ $history->id }}"
            data-name="{{ $history->cattle?->code }} - {{ $history->owner?->business_name ?: $history->owner?->full_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
