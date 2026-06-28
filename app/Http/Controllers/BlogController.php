<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::published()
            ->with('author')
            ->latest('published_at')
            ->paginate(9);

        return view('public.blog.index', compact('posts'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::published()
            ->with('author')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.blog.show', compact('post'));
    }
}
