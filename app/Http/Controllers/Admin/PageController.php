<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.index');
    }


    public function list()
    {
        $pages = Page::query()->orderByDesc('id');

        return datatables()
            ->eloquent($pages)
            ->addIndexColumn()

            ->editColumn('title', fn($page) => e($page->title))

            ->editColumn('slug', fn($page) => e($page->slug))

            ->editColumn('status', function ($page) {
                return $page->status === 'published'
                    ? '<span class="badge bg-success rounded-pill px-3 py-2">
                    <i class="bi bi-check-circle me-1"></i> Publicado
                   </span>'
                    : '<span class="badge bg-secondary rounded-pill px-3 py-2">
                    <i class="bi bi-pencil-square me-1"></i> Borrador
                   </span>';
            })

            ->addColumn('acciones', function ($page) {
                return view('admin.pages.partials.acciones', compact('page'))->render();
            })

            ->rawColumns(['status', 'acciones'])
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
            'title' => 'required|string|min:5|max:255',
            'slug'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug'),
            ],
            'content' => 'required|string',
            'status'  => 'nullable|string|in:published',
        ], [
            'title.required'   => 'El título es obligatorio.',
            'title.min'        => 'El título debe tener al menos 5 caracteres.',
            'slug.required'    => 'El slug es obligatorio.',
            'slug.unique'      => 'Este slug ya está en uso.',
            'content.required' => 'El contenido es obligatorio.',
        ]);

        $data['status']  = $request->has('status') ? 'published' : 'draft';
        $data['user_id'] = Auth::id();

        try {
            DB::beginTransaction();

            $page = Page::create($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Página creada correctamente.',
                'data'    => $page,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando página: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear la página.',
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return response()->json($page);
    }


    public function showView(Page $page)
    {
        return response()->json([
            'id'         => $page->id,
            'title'      => $page->title,
            'slug'       => $page->slug,
            'content'    => $page->content, // HTML REAL (TinyMCE)
            'status'     => $page->status,
            'created_at' => optional($page->created_at)->format('d/m/Y h:i A'),
            'updated_at' => optional($page->updated_at)->format('d/m/Y h:i A'),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required|string|min:5|max:255',
            'slug'  => [
                'required',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($page->id),
            ],
            'content' => 'required|string',
            'status'  => 'nullable|string|in:published',
        ], [
            'title.required'   => 'El título es obligatorio.',
            'title.min'        => 'El título debe tener al menos 5 caracteres.',
            'slug.required'    => 'El slug es obligatorio.',
            'slug.unique'      => 'Este slug ya está en uso.',
            'content.required' => 'El contenido es obligatorio.',
        ]);

        $data['status'] = $request->has('status') ? 'published' : 'draft';

        try {
            DB::beginTransaction();

            $page->update($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Página actualizada correctamente.',
                'data'    => $page,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error actualizando página: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar la página.',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    

    public function destroy(string $id)
    {
        try {
            $page = Page::findOrFail($id);

            $page->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Página eliminada correctamente.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'La página no existe o ya fue eliminada.'
            ], 404);
        } catch (\Throwable $e) {

            Log::error('Error eliminando página: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error al eliminar la página.'
            ], 500);
        }
    }
}
