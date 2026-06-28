<div class="d-flex justify-content-center align-items-center">
    @can('admin.treatments.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewTreatment mr-1" title="Ver detalle"
            data-id="{{ $treatment->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.treatments.update')
        <button type="button" class="btn btn-outline-info btn-xs editTreatment mr-1" title="Editar tratamiento"
            data-id="{{ $treatment->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.treatments.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteTreatment" title="Eliminar tratamiento"
            data-id="{{ $treatment->id }}"
            data-name="{{ $treatment->treatment_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
