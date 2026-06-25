<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(BlogPost $blogPost) {}

    public function edit(BlogPost $blogPost) {}

    public function update(Request $request, BlogPost $blogPost) {}

    public function destroy(BlogPost $blogPost) {}
}
