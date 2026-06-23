<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- EDITAR --}}
    <button type="button"
        class="btn btn-outline-info btn-xs editPriceType"
        title="Editar tipo de precio"
        data-id="{{ $pt->id }}"
        data-name="{{ $pt->name }}"
        data-code="{{ $pt->code }}"
        data-status="{{ $pt->status }}">

        <i class="fas fa-pen"></i>
    </button>

    {{-- ELIMINAR --}}
    <button type="button"
        class="btn btn-outline-danger btn-xs deletePriceType"
        title="Eliminar tipo de precio"
        data-id="{{ $pt->id }}">

        <i class="fas fa-trash"></i>
    </button>

</div>