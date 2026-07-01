<section class="section news premium-news" id="blog">
    <div class="container">
        <div class="section-heading heading-row js-reveal">
            <div>
                <span class="eyebrow"><span></span>Blog / Noticias</span>
                <h2>Actualidad para productores y criadores</h2>
            </div>
            <a class="text-link" href="{{ route('public.blog.index') }}">Ver todas las noticias <span>&rarr;</span></a>
        </div>

        @if (($latestPosts ?? collect())->isEmpty())
            <article class="news-card news-empty js-reveal">
                <div class="news-body">
                    <span class="category">Noticias</span>
                    <h3>Proximamente</h3>
                    <p>Pronto compartiremos novedades, actividades y conocimiento para fortalecer la crianza bovina.</p>
                </div>
            </article>
        @else
            <div class="news-grid">
                @foreach ($latestPosts as $post)
                    <article class="news-card js-reveal">
                        @if ($post->image_path)
                            <img class="news-art-img" loading="lazy" src="{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}" alt="{{ $post->title }}">
                        @else
                            <div class="news-art certificate"><i class="fas fa-newspaper"></i></div>
                        @endif
                        <div class="news-body">
                            <span class="category">{{ $post->published_at?->format('d/m/Y') ?: 'Noticias' }}</span>
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 130) }}</p>
                            <a href="{{ route('public.blog.show', $post->slug) }}" aria-label="Leer mas sobre {{ $post->title }}">Leer mas <span>&rarr;</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
