<div class="d-flex justify-content-center align-items-center gap-2">

    <button type="button" class="btn btn-outline-info btn-xs editBrand" title="Editar marca" data-id="{{ $brand->id }}"
        data-name="{{ $brand->name }}" data-slug="{{ $brand->slug }}" data-status="{{ $brand->status }}"
        data-image="{{ optional($brand->images->first())->image }}">

        <i class="fas fa-pen"></i>
    </button>

    <button type="button" class="btn btn-outline-danger btn-xs deleteBrand" title="Eliminar marca"
        data-id="{{ $brand->id }}">

        <i class="fas fa-trash"></i>
    </button>

</div>

