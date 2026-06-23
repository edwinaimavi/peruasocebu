<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- EDITAR --}}

    <button type="button" class="btn btn-outline-secondary btn-xs viewPurchase" data-id="{{ $purchase->id }}"
        title="Ver compra">
        <i class="fas fa-eye"></i>
    </button>
    <button type="button" class="btn btn-outline-info btn-xs editPurchase" title="Editar compra"
        data-id="{{ $purchase->id }}" data-supplier_id="{{ $purchase->supplier_id }}"
        data-document_type="{{ $purchase->document_type }}" data-document_number="{{ $purchase->document_number }}"
        data-date="{{ $purchase->date }}" data-total="{{ $purchase->total }}" data-status="{{ $purchase->status }}">

        <i class="fas fa-pen"></i>
    </button>

    {{-- ELIMINAR --}}
    <button type="button" class="btn btn-outline-danger btn-xs deletePurchase" title="Eliminar compra"
        data-id="{{ $purchase->id }}">

        <i class="fas fa-trash"></i>
    </button>

</div>
