<div class="d-flex justify-content-center align-items-center">
    @can('admin.veterinarians.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewVeterinarian mr-1" title="Ver detalles"
            data-id="{{ $veterinarian->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.veterinarians.update')
        <button type="button" class="btn btn-outline-info btn-xs editVeterinarian mr-1" title="Editar veterinario"
            data-id="{{ $veterinarian->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.veterinarians.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteVeterinarian" title="Eliminar veterinario"
            data-id="{{ $veterinarian->id }}" data-name="{{ $veterinarian->full_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
