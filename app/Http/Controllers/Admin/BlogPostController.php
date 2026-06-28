<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BlogPostController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.blog-posts.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.blog-posts.store')->only('store');
        $this->middleware('can:admin.blog-posts.update')->only('update', 'publish', 'draft');
        $this->middleware('can:admin.blog-posts.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.blog_posts.index', [
            'statuses' => $this->statuses(),
        ]);
    }

    public function list(): JsonResponse
    {
        $posts = BlogPost::query()
            ->with('author')
            ->latest('id');

        return DataTables::eloquent($posts)
            ->addIndexColumn()
            ->addColumn('image', fn (BlogPost $post) => $this->tableImage($post))
            ->addColumn('author_name', fn (BlogPost $post) => $post->author?->name ?: '-')
            ->editColumn('status', fn (BlogPost $post) => $this->statusBadge($post->status))
            ->editColumn('published_at', fn (BlogPost $post) => $post->published_at?->format('d/m/Y H:i') ?: 'Sin publicar')
            ->editColumn('created_at', fn (BlogPost $post) => $post->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (BlogPost $post) => view(
                'admin.blog_posts.partials.acciones',
                compact('post')
            )->render())
            ->rawColumns(['image', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureContentIsNotBlank($request);

        $data['content'] = $this->sanitizeContent($request->input('content'));
        $data['slug'] = $this->generateUniqueSlug($data['title']);
        $data['author_id'] = auth()->id();
        $data = $this->normalizePublicationData($data);
        $image = $this->storeImage($request);

        try {
            BlogPost::create(array_merge($data, $image));
        } catch (\Throwable $exception) {
            $this->deleteImage($image['image_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Publicacion registrada correctamente.',
        ]);
    }

    public function show(BlogPost $blogPost): JsonResponse
    {
        $blogPost->load('author');

        return response()->json([
            'post' => $this->postPayload($blogPost),
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureContentIsNotBlank($request);

        $data['content'] = $this->sanitizeContent($request->input('content'));
        $data['slug'] = $this->generateUniqueSlug($data['title'], $blogPost->id);
        $data = $this->normalizePublicationData($data, $blogPost);
        $image = $this->storeImage($request);
        $oldImagePath = $image ? $blogPost->image_path : null;

        try {
            $blogPost->update(array_merge($data, $image));
        } catch (\Throwable $exception) {
            $this->deleteImage($image['image_path'] ?? null);
            throw $exception;
        }

        $this->deleteImage($oldImagePath);

        return response()->json([
            'message' => 'Publicacion actualizada correctamente.',
        ]);
    }

    public function destroy(BlogPost $blogPost): JsonResponse
    {
        $blogPost->delete();

        return response()->json([
            'message' => 'Publicacion eliminada correctamente.',
        ]);
    }

    public function publish(BlogPost $blogPost): JsonResponse
    {
        $blogPost->update([
            'status' => 'published',
            'published_at' => $blogPost->published_at ?: now(),
        ]);

        return response()->json([
            'message' => 'Publicacion publicada correctamente.',
        ]);
    }

    public function draft(BlogPost $blogPost): JsonResponse
    {
        $blogPost->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return response()->json([
            'message' => 'Publicacion enviada a borrador correctamente.',
        ]);
    }

    public function create() {}

    public function edit(BlogPost $blogPost) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
            'published_at' => ['nullable', 'date'],
        ], [
            'title.required' => 'Ingrese el titulo.',
            'content.required' => 'Ingrese el contenido de la publicacion.',
            'image_file.image' => 'La imagen debe ser JPG, PNG o WEBP y no superar los 4 MB.',
            'image_file.mimes' => 'La imagen debe ser JPG, PNG o WEBP y no superar los 4 MB.',
            'image_file.max' => 'La imagen debe ser JPG, PNG o WEBP y no superar los 4 MB.',
            'status.required' => 'Seleccione un estado valido.',
            'status.in' => 'Seleccione un estado valido.',
            'published_at.date' => 'La fecha de publicacion no es valida.',
        ]);
    }

    private function ensureContentIsNotBlank(Request $request): void
    {
        $plainContent = trim(html_entity_decode(strip_tags((string) $request->input('content'))));

        if ($plainContent === '') {
            throw ValidationException::withMessages([
                'content' => 'Ingrese el contenido de la publicacion.',
            ]);
        }
    }

    private function sanitizeContent(string $content): string
    {
        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content) ?? '';
        $content = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $content) ?? '';
        $content = preg_replace('/\son\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $content) ?? '';
        $content = preg_replace('/javascript\s*:/is', '', $content) ?? '';

        return $content;
    }

    private function normalizePublicationData(array $data, ?BlogPost $post = null): array
    {
        if (($data['status'] ?? null) === 'published') {
            $data['published_at'] = $data['published_at'] ?? $post?->published_at ?? now();

            return $data;
        }

        $data['published_at'] = null;

        return $data;
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title) ?: 'publicacion';
        $slug = $baseSlug;
        $counter = 2;

        while (BlogPost::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function storeImage(Request $request): array
    {
        if (! $request->hasFile('image_file')) {
            return [];
        }

        return [
            'image_path' => $request->file('image_file')->store('blog/posts', 'public'),
        ];
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function postPayload(BlogPost $post): array
    {
        return array_merge($post->toArray(), [
            'author_name' => $post->author?->name ?: '-',
            'status_label' => $this->statusLabel($post->status),
            'image_url' => $this->fileUrl($post->image_path),
            'public_url' => $post->status === 'published' ? route('public.blog.show', $post->slug) : null,
            'published_at' => $post->published_at?->format('Y-m-d\TH:i'),
            'published_at_formatted' => $post->published_at?->format('d/m/Y H:i') ?: 'Sin publicar',
            'created_at_formatted' => $post->created_at?->format('d/m/Y H:i'),
            'updated_at_formatted' => $post->updated_at?->format('d/m/Y H:i'),
        ]);
    }

    private function tableImage(BlogPost $post): string
    {
        $url = $this->fileUrl($post->image_path);

        if (! $url) {
            return '<span class="blog-table-photo blog-table-photo-placeholder"><i class="fas fa-newspaper"></i></span>';
        }

        return '<img class="blog-table-photo" src="'.e($url).'" alt="Imagen de '.e($post->title).'">';
    }

    private function statuses(): array
    {
        return [
            'draft' => 'Borrador',
            'published' => 'Publicado',
        ];
    }

    private function statusLabel(?string $status): string
    {
        return $this->statuses()[$status] ?? '-';
    }

    private function statusBadge(?string $status): string
    {
        $classes = [
            'draft' => 'badge-warning',
            'published' => 'badge-success',
        ];

        return '<span class="badge '.($classes[$status] ?? 'badge-secondary').'">'.$this->statusLabel($status).'</span>';
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
