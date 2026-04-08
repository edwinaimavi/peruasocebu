<div class="d-flex justify-content-center align-items-center gap-2">

    @can('admin.roles.update')
        <button type="button" class="btn btn-outline-info btn-xs editRole" data-id="{{ $role->id }}"
            data-name="{{ $role->name }}" title="Editar rol">
            <i class="fas fa-pen"></i>
        </button>-
    @endcan

    @can('admin.roles.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteRole" data-id="{{ $role->id }}"
            title="Eliminar rol">
            <i class="fas fa-trash"></i>
        </button>
    @endcan

</div>
