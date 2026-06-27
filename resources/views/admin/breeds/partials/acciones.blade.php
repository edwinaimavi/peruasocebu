<div class="d-flex justify-content-center align-items-center">
    @can('admin.breeds.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewBreed mr-1" title="Ver detalles"
            data-id="{{ $breed->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.breeds.update')
        <button type="button" class="btn btn-outline-info btn-xs editBreed mr-1" title="Editar raza"
            data-id="{{ $breed->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.breeds.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteBreed" title="Eliminar raza"
            data-id="{{ $breed->id }}" data-name="{{ $breed->name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
