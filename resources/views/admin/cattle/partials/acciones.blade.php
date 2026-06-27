<div class="d-flex justify-content-center align-items-center">
    @can('admin.cattle.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewCattle mr-1" title="Ver detalles"
            data-id="{{ $cattle->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.cattle.update')
        <button type="button" class="btn btn-outline-info btn-xs editCattle mr-1" title="Editar ganado"
            data-id="{{ $cattle->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.cattle.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteCattle" title="Eliminar ganado"
            data-id="{{ $cattle->id }}" data-name="{{ $cattle->name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
