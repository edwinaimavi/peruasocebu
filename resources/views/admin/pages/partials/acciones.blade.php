<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- VER --}}
    <button type="button" class="btn btn-outline-secondary btn-xs viewPage" title="Ver página"
        data-id="{{ $page->id }}">
        <i class="fas fa-eye"></i>
    </button>-

    {{-- EDITAR --}}
    <button type="button" class="btn btn-outline-info btn-xs editPage" title="Editar página" data-id="{{ $page->id }}"
        data-title="{{ $page->title }}" data-slug="{{ $page->slug }}" 
        data-status="{{ $page->status }}">
        <i class="fas fa-pen"></i>
    </button>-

    {{-- ELIMINAR --}}
    <button type="button" class="btn btn-outline-danger btn-xs deletePage" title="Eliminar página"
        data-id="{{ $page->id }}">
        <i class="fas fa-trash"></i>
    </button>

</div>
