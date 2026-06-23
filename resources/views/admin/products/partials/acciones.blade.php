<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- VER --}}
    <button type="button" class="btn btn-outline-secondary btn-xs viewProduct" title="Ver producto"
        data-id="{{ $product->id }}">
        <i class="fas fa-eye"></i>
    </button>-

    {{-- EDITAR --}}
    <button type="button" class="btn btn-outline-info btn-xs editProduct" title="Editar producto"
        data-id="{{ $product->id }}" data-category_id="{{ $product->category_id }}" data-name="{{ $product->name }}" data-model="{{ $product->model }}"
        data-slug="{{ $product->slug }}" data-short_description="{{ e($product->short_description) }}"
        data-description="{{ e($product->description) }}" data-price="{{ $product->price }}"
        data-type="{{ $product->type }}" data-status="{{ $product->status }}"
        data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}"
        data-brand="{{ $product->brand_id }}">
        <i class="fas fa-pen"></i>
    </button>-

    {{-- ELIMINAR --}}
    <button type="button" class="btn btn-outline-danger btn-xs deleteProduct" title="Eliminar producto"
        data-id="{{ $product->id }}">
        <i class="fas fa-trash"></i>
    </button>

</div>
