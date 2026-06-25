<div class="d-flex justify-content-center align-items-center">
    @can('admin.ranches.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewRanch mr-1" title="Ver detalles"
            data-id="{{ $ranch->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.ranches.update')
        <button type="button" class="btn btn-outline-info btn-xs editRanch mr-1" title="Editar criadero"
            data-id="{{ $ranch->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.ranches.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteRanch" title="Eliminar criadero"
            data-id="{{ $ranch->id }}" data-name="{{ $ranch->name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
