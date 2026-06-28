<div class="d-flex justify-content-center align-items-center">
    @can('admin.vaccinations.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewVaccination mr-1" title="Ver detalle"
            data-id="{{ $vaccination->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.vaccinations.update')
        <button type="button" class="btn btn-outline-info btn-xs editVaccination mr-1" title="Editar vacuna"
            data-id="{{ $vaccination->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.vaccinations.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteVaccination" title="Eliminar vacuna"
            data-id="{{ $vaccination->id }}"
            data-name="{{ $vaccination->vaccine_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
