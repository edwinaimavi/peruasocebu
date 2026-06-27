<div class="d-flex justify-content-center align-items-center">
    @can('admin.cattle-genealogy.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewGenealogy mr-1" title="Ver detalles"
            data-id="{{ $link->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.cattle-genealogy.update')
        <button type="button" class="btn btn-outline-info btn-xs editGenealogy mr-1" title="Editar genealogía"
            data-id="{{ $link->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.cattle-genealogy.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteGenealogy" title="Eliminar genealogía"
            data-id="{{ $link->id }}" data-name="{{ $link->relative_name ?: $link->relativeCattle?->name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
