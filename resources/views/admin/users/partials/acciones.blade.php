<div class="d-flex justify-content-center align-items-center gap-2">

    @can('admin.users.update')
        <button type="button" class="btn btn-outline-info btn-xs editUser" title="Editar usuario" data-id="{{ $user->id }}"
            data-dni="{{ $user->dni }}" data-name="{{ $user->name }}" data-lastname="{{ $user->lastname }}"
            data-email="{{ $user->email }}" data-phone="{{ $user->phone }}" data-address="{{ $user->address }}"
            data-status="{{ $statusOriginal }}" data-role="{{ $rol }}" data-photo="{{ $rutaFoto }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.users.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteUser" title="Eliminar usuario"
            data-id="{{ $user->id }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan

</div>
