<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- EDITAR --}}
    <button type="button" class="btn btn-outline-info btn-xs btn-edit" title="Editar cliente" data-id="{{ $customer->id }}"
        data-first_name="{{ $customer->first_name }}" data-last_name="{{ $customer->last_name }}"
        data-document_type="{{ $customer->document_type }}" data-document_number="{{ $customer->document_number }}"
        data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}" data-address="{{ $customer->address }}"
        data-status="{{ $customer->status }}">

        <i class="fas fa-pen"></i>
    </button>

    {{-- ELIMINAR --}}
    <button type="button" class="btn btn-outline-danger btn-xs btn-delete" title="Eliminar cliente"
        data-id="{{ $customer->id }}">

        <i class="fas fa-trash"></i>
    </button>

</div>
