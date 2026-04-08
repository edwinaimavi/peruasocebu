<div class="d-flex justify-content-center align-items-center gap-2">

    {{-- VER --}}
    <button type="button" class="btn btn-outline-secondary btn-xs viewPost" title="Ver post" data-id="{{ $post->id }}">
        <i class="fas fa-eye"></i>
    </button>-

    {{-- EDITAR --}}
    <button type="button" class="btn btn-outline-info btn-xs editPost" title="Editar post" data-id="{{ $post->id }}"
        data-title="{{ $post->title }}" data-slug="{{ $post->slug }}" data-content="{{ e($post->content) }}"
        data-status="{{ $post->status }}" data-category_id="{{ $post->category_id }}"
        data-image="{{ $post->image ? asset('storage/' . $post->image) : '' }}">
        <i class="fas fa-pen"></i>
    </button>-

    {{-- ELIMINAR --}}
    <button type="button" class="btn btn-outline-danger btn-xs deletePost" title="Eliminar post"
        data-id="{{ $post->id }}">
        <i class="fas fa-trash"></i>
    </button>

</div>
