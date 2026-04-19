<div class="d-flex justify-content-center align-items-center gap-2">
    {{-- 
    @can('admin.categories.update') --}}
    <button type="button" class="btn btn-outline-info btn-xs editCategory" title="Editar categoría"
        data-id="{{ $category->id }}" data-name="{{ $category->name }}" data-slug="{{ $category->slug }}"
        data-description="{{ $category->description }}" data-status="{{ $category->status }}"
        data-image="{{ optional($category->images->first())->image }} " data-parent="{{ $category->parent_id }}">
        <i class="fas fa-pen"></i>
    </button>-
    {{--  @endcan --}}
    {{-- 
    @can('admin.categories.destroy') --}}
    <button type="button" class="btn btn-outline-danger btn-xs deleteCategory" title="Eliminar categoría"
        data-id="{{ $category->id }}">
        <i class="fas fa-trash"></i>
    </button>
    {{--   @endcan --}}

</div>
