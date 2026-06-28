<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog y Noticias | PERU ASOCEBU</title>
    @vite('resources/css/public-home.css')
    <style>
        body { background: #f5f7f3; color: #20372b; }
        .blog-page { padding: 110px 0 56px; }
        .blog-hero { margin-bottom: 28px; }
        .blog-hero h1 { font-size: clamp(2rem, 5vw, 4rem); margin: 0 0 10px; }
        .blog-grid { display: grid; gap: 22px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .blog-card { background: #fff; border: 1px solid rgba(31, 77, 54, .12); border-radius: 14px; box-shadow: 0 16px 34px rgba(31, 77, 54, .08); overflow: hidden; }
        .blog-card img { height: 190px; object-fit: cover; width: 100%; }
        .blog-card-body { padding: 18px; }
        .blog-meta { color: #7b6a43; font-size: .82rem; font-weight: 700; text-transform: uppercase; }
        .blog-card h2 { font-size: 1.25rem; margin: 8px 0; }
        .blog-card p { color: #5d6b62; line-height: 1.65; }
        .blog-empty { background: #fff; border-radius: 14px; padding: 28px; text-align: center; }
        .pagination { display: flex; gap: 8px; justify-content: center; list-style: none; padding: 0; }
        .page-link { background: #fff; border: 1px solid rgba(31, 77, 54, .16); border-radius: 8px; color: #1f4d36; display: block; padding: 8px 12px; text-decoration: none; }
        .page-item.active .page-link { background: #1f4d36; color: #fff; }
        .page-item.disabled .page-link { color: #9aa49d; }
        @media (max-width: 900px) { .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 620px) { .blog-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="{{ route('public.home') }}">
                <span class="brand-mark" aria-hidden="true">PA</span>
                <span><strong>PERU ASOCEBU</strong><small>Genetica que deja huella</small></span>
            </a>
            <nav class="main-nav is-open" aria-label="Navegacion principal">
                <a href="{{ route('public.home') }}">Inicio</a>
                <a href="{{ route('public.blog.index') }}">Blog</a>
                @auth
                    <a href="{{ route('home') }}">Panel</a>
                @else
                    <a href="{{ route('login') }}">Iniciar sesion</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="blog-page">
        <div class="container">
            <section class="blog-hero">
                <span class="eyebrow"><span></span>Actualidad ganadera</span>
                <h1>Blog y Noticias</h1>
                <p>Publicaciones, novedades y conocimiento para fortalecer la crianza bovina.</p>
            </section>

            @if ($posts->isEmpty())
                <div class="blog-empty">Proximamente compartiremos noticias y novedades de nuestro criadero.</div>
            @else
                <div class="blog-grid">
                    @foreach ($posts as $post)
                        <article class="blog-card">
                            @if ($post->image_path)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}" alt="{{ $post->title }}">
                            @endif
                            <div class="blog-card-body">
                                <div class="blog-meta">
                                    {{ $post->published_at?->format('d/m/Y') }} &middot; {{ $post->author?->name ?: 'PERU ASOCEBU' }}
                                </div>
                                <h2>{{ $post->title }}</h2>
                                <p>{{ $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}</p>
                                <a class="text-link" href="{{ route('public.blog.show', $post->slug) }}">Leer mas <span>&rarr;</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </main>
</body>
</html>
