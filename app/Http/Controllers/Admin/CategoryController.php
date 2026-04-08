<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.categories.index');
    }


    public function list()
    {
        $categories = Category::query()->orderByDesc('id');

        return DataTables::eloquent($categories)
            ->addIndexColumn()

            ->editColumn('name', fn($category) => e($category->name))

            ->editColumn('description', function ($category) {
                return $category->description
                    ? '<span class="text-muted">' . e(Str::limit($category->description, 60)) . '</span>'
                    : '<span class="text-muted">—</span>';
            })

            ->editColumn('status', function ($category) {
                return $category->status
                    ? '<span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i> Activo
                   </span>'
                    : '<span class="badge bg-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-slash-circle me-1"></i> Inactivo
                   </span>';
            })

            ->addColumn('acciones', function ($category) {
                return view('admin.categories.partials.acciones', compact('category'))->render();
            })

            ->rawColumns(['description', 'status', 'acciones'])
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
            'name' => 'required|string|min:3|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug'),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'Este slug ya está en uso.',
        ]);

        $data['status'] = $request->has('status') ? 1 : 0;

        try {
            DB::beginTransaction();

            $category = Category::create($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Categoría creada correctamente.',
                'data'    => $category,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando categoría: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear la categoría.',
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'Este slug ya está en uso.',
        ]);

        $data['status'] = $request->has('status') ? 1 : 0;

        try {
            DB::beginTransaction();

            $category->update($data);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Categoría actualizada correctamente.',
                'data'    => $category,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error actualizando categoría: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar la categoría.',
            ], 500);
        }
    }


   public function destroy(string $id)
{
    try {
        $category = Category::findOrFail($id);

        // (Opcional) Si luego tienes relaciones, aquí puedes validar:
        // if ($category->products()->exists()) {
        //     return response()->json([
        //         'status'  => 'error',
        //         'message' => 'No se puede eliminar la categoría porque tiene productos asociados.'
        //     ], 422);
        // }

        $category->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Categoría eliminada correctamente.'
        ], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

        return response()->json([
            'status'  => 'error',
            'message' => 'La categoría no existe o ya fue eliminada.'
        ], 404);

    } catch (\Throwable $e) {

        Log::error('Error eliminando categoría: ' . $e->getMessage());

        return response()->json([
            'status'  => 'error',
            'message' => 'Ocurrió un error al eliminar la categoría.'
        ], 500);
    }
}

}
