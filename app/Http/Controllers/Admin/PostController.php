<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.posts.index', compact('categories'));
    }


    public function list()
    {
        $posts = Post::with('user')->orderByDesc('id');

        return DataTables::eloquent($posts)
            ->addIndexColumn()

            ->editColumn('title', fn($post) => e($post->title))

            ->editColumn('user_id', function ($post) {
                return $post->user
                    ? '<span class="fw-semibold">' . e($post->user->name) . '</span>'
                    : '<span class="text-muted">—</span>';
            })

            ->editColumn('status', function ($post) {
                return $post->status === 'published'
                    ? '<span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i> Publicado
                   </span>'
                    : '<span class="badge bg-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-pencil-square me-1"></i> Borrador
                   </span>';
            })

            ->addColumn('acciones', function ($post) {
                return view('admin.posts.partials.acciones', compact('post'))->render();
            })

            ->rawColumns(['user_id', 'status', 'acciones'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'slug'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug'),
            ],
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'nullable|string|in:published',
        ], [
            'title.required'       => 'El título es obligatorio.',
            'title.min'            => 'El título debe tener al menos 5 caracteres.',
            'slug.required'        => 'El slug es obligatorio.',
            'slug.unique'          => 'Este slug ya está en uso.',
            'content.required'     => 'El contenido es obligatorio.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no es válida.',
            'image.image'          => 'El archivo debe ser una imagen válida.',
            'image.mimes'          => 'La imagen debe ser JPG, PNG o WEBP.',
            'image.max'            => 'La imagen no debe pesar más de 5MB.',
        ]);

        $data['status']  = $request->has('status') ? 'published' : 'draft';
        $data['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            /* sleep(5); // 5 segundos */


            // Guardar imagen si existe
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('posts', 'public');
            }

            $post = Post::create($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Post creado correctamente.',
                'data'    => $post,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando post: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear el post.',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function showView(Post $post)
    {
        $post->load(['user', 'category']);

        return response()->json([
            'id'         => $post->id,
            'title'      => $post->title,
            'slug'       => $post->slug,
            'content'    => $post->content,
            'status'     => $post->status,
            'image'      => $post->image ? asset('storage/' . $post->image) : null,
            'author'     => $post->user?->name ?? '—',
            'category'   => $post->category?->name ?? '—',
            'created_at' => optional($post->created_at)->format('d/m/Y h:i A'),
            'updated_at' => optional($post->updated_at)->format('d/m/Y h:i A'),
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return response()->json([
            'id'          => $post->id,
            'title'       => $post->title,
            'slug'        => $post->slug,
            'content'     => $post->content, // ⬅️ HTML REAL
            'status'      => $post->status,
            'category_id' => $post->category_id,
            'image'       => $post->image ? asset('storage/' . $post->image) : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'       => 'required|string|min:5|max:255',
            'slug'        => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($post->id),
            ],
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'      => 'nullable|string|in:published',
        ], [
            'title.required'       => 'El título es obligatorio.',
            'title.min'            => 'El título debe tener al menos 5 caracteres.',
            'slug.required'        => 'El slug es obligatorio.',
            'slug.unique'          => 'Este slug ya está en uso.',
            'content.required'     => 'El contenido es obligatorio.',
            'category_id.required' => 'La categoría es obligatoria.',
            'category_id.exists'   => 'La categoría seleccionada no es válida.',
            'image.image'          => 'El archivo debe ser una imagen válida.',
            'image.mimes'          => 'La imagen debe ser JPG, PNG o WEBP.',
            'image.max'            => 'La imagen no debe pesar más de 5MB.',
        ]);

        $data['status'] = $request->has('status') ? 'published' : 'draft';

        try {
            DB::beginTransaction();


            // Actualizar imagen si hay nueva
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('posts', 'public');
            }

            $post->update($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Post actualizado correctamente.',
                'data'    => $post,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error actualizando post: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar el post.',
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);

            // 🧹 eliminar imagen si existe
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }

            $post->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Post eliminado correctamente.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'El post no existe o ya fue eliminado.'
            ], 404);
        } catch (\Throwable $e) {

            Log::error('Error eliminando post: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error al eliminar el post.'
            ], 500);
        }
    }


    public function meta(Post $post)
    {
        return response()->json([
            'author'     => $post->user?->name ?? '—',
            'created_at' => optional($post->created_at)->format('d/m/Y h:i A'),
            'image'      => $post->image ? asset('storage/' . $post->image) : null,
        ]);
    }
}
