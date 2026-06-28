<div class="d-flex justify-content-center align-items-center">
    @can('admin.blog-posts.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewBlogPost mr-1" title="Ver detalle"
            data-id="{{ $post->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.blog-posts.update')
        <button type="button" class="btn btn-outline-info btn-xs editBlogPost mr-1" title="Editar publicacion"
            data-id="{{ $post->id }}">
            <i class="fas fa-pen"></i>
        </button>

        @if ($post->status !== 'published')
            <button type="button" class="btn btn-outline-success btn-xs publishBlogPost mr-1" title="Publicar"
                data-id="{{ $post->id }}" data-name="{{ $post->title }}">
                <i class="fas fa-upload"></i>
            </button>
        @else
            <button type="button" class="btn btn-outline-warning btn-xs draftBlogPost mr-1" title="Pasar a borrador"
                data-id="{{ $post->id }}" data-name="{{ $post->title }}">
                <i class="fas fa-archive"></i>
            </button>
            <a class="btn btn-outline-success btn-xs mr-1" title="Ver en pagina publica"
                href="{{ route('public.blog.show', $post->slug) }}" target="_blank" rel="noopener">
                <i class="fas fa-external-link-alt"></i>
            </a>
        @endif
    @endcan

    @can('admin.blog-posts.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteBlogPost" title="Eliminar publicacion"
            data-id="{{ $post->id }}" data-name="{{ $post->title }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
