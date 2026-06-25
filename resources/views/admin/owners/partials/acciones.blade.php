<div class="d-flex justify-content-center align-items-center">
    @can('admin.owners.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewOwner mr-1" title="Ver detalles"
            data-id="{{ $owner->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.owners.update')
        <button type="button" class="btn btn-outline-info btn-xs editOwner mr-1" title="Editar propietario"
            data-id="{{ $owner->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.owners.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteOwner" title="Eliminar propietario"
            data-id="{{ $owner->id }}"
            data-name="{{ $owner->business_name ?: $owner->full_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
