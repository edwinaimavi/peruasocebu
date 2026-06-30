@extends('public.layouts.app')

@section('title', $post->title . ' | PERU ASOCEBU')
@section('meta_description', $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155))
@section('main_class', 'post-page')

@push('styles')
    <style>
        body { background: #f5f7f3; color: #20372b; }
        .post-page { padding: 110px 0 56px; }
        .post-shell { margin: 0 auto; max-width: 920px; }
        .post-hero { margin-bottom: 24px; }
        .post-hero h1 { font-size: clamp(2rem, 5vw, 4rem); line-height: 1.05; margin: 8px 0 12px; }
        .post-meta { color: #7b6a43; font-size: .9rem; font-weight: 700; text-transform: uppercase; }
        .post-image { border-radius: 18px; box-shadow: 0 18px 40px rgba(31, 77, 54, .14); max-height: 460px; object-fit: cover; width: 100%; }
        .post-card { background: #fff; border: 1px solid rgba(31, 77, 54, .12); border-radius: 16px; box-shadow: 0 16px 34px rgba(31, 77, 54, .08); margin-top: 24px; padding: clamp(20px, 4vw, 42px); }
        .blog-content { font-size: 1rem; line-height: 1.8; }
        .blog-content img { border-radius: 12px; height: auto; max-width: 100%; }
        .blog-content a { color: #1f4d36; text-decoration: underline; }
        .blog-content table { border-collapse: collapse; margin: 1rem 0; width: 100%; }
        .blog-content table td, .blog-content table th { border: 1px solid #dfe8e2; padding: 8px; }
    </style>
@endpush

@section('content')
    <article class="container post-shell">
        <a class="text-link" href="{{ route('public.blog.index') }}"><span>&larr;</span> Volver al blog</a>
        <header class="post-hero">
            <span class="eyebrow"><span></span>Blog / Noticias</span>
            <h1>{{ $post->title }}</h1>
            <div class="post-meta">
                {{ $post->published_at?->format('d/m/Y') }} &middot; {{ $post->author?->name ?: 'PERU ASOCEBU' }}
            </div>
            @if ($post->summary)
                <p>{{ $post->summary }}</p>
            @endif
        </header>

        @if ($post->image_path)
            <img class="post-image" src="{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}" alt="{{ $post->title }}">
        @endif

        <section class="post-card">
            <div class="blog-content">
                {!! $post->content !!}
            </div>
        </section>
    </article>
@endsection
