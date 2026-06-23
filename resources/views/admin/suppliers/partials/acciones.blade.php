<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- EDITAR --}}
    <button type="button"
        class="btn btn-outline-info btn-xs editSupplier"
        title="Editar proveedor"
        data-id="{{ $supplier->id }}"
        data-name="{{ $supplier->name }}"
        data-ruc="{{ $supplier->ruc }}"
        data-phone="{{ $supplier->phone }}"
        data-email="{{ $supplier->email }}"
        data-address="{{ $supplier->address }}"
        data-status="{{ $supplier->status }}">

        <i class="fas fa-pen"></i>
    </button>

    {{-- ELIMINAR --}}
    <button type="button"
        class="btn btn-outline-danger btn-xs deleteSupplier"
        title="Eliminar proveedor"
        data-id="{{ $supplier->id }}">

        <i class="fas fa-trash"></i>
    </button>

</div>