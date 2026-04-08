<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.products.index', compact('categories'));
    }


    public function list()
    {
        $products = Product::with('category')
            ->where('status', '!=', 'removed')
            ->orderByDesc('id');
        return DataTables::eloquent($products)
            ->addIndexColumn()

            ->editColumn('name', fn($product) => e($product->name))

            ->editColumn('category_id', function ($product) {
                return $product->category
                    ? '<span class="fw-semibold">' . e($product->category->name) . '</span>'
                    : '<span class="text-muted">—</span>';
            })

            ->editColumn('type', function ($product) {
                return $product->type === 'sistema'
                    ? '<span class="badge badge-sistema px-3 py-2">Sistema</span>'
                    : '<span class="badge badge-servicio px-3 py-2">Servicio</span>';
            })

            ->editColumn('price', function ($product) {
                return '<span class="fw-bold">S/ ' . number_format($product->price, 2) . '</span>';
            })

            ->editColumn('status', function ($product) {

                if ($product->status === 'published') {
                    return '<span class="badge bg-success rounded-pill px-3 py-2">
                    <i class="fas fa-check-circle me-1"></i> Publicado
                </span>';
                }

                return '<span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                <i class="fas fa-clock me-1"></i> Borrador
            </span>';
            })

            ->addColumn('acciones', function ($product) {
                return view('admin.products.partials.acciones', compact('product'))->render();
            })

            ->rawColumns(['category_id', 'type', 'price', 'status', 'acciones'])
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
            'name'              => 'required|string|min:3|max:255',
            'slug'              => ['required', 'string', 'max:255', Rule::unique('products', 'slug')],
            'category_id'       => 'nullable|exists:categories,id',
            'short_description' => 'nullable|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',
            'type'              => 'required|in:sistema,servicio',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'            => 'nullable|in:published',
        ]);

        $data['status'] = $request->has('status') ? 'published' : 'draft';

        try {
            DB::beginTransaction();

            // ✅ 1. CREAR PRODUCTO PRIMERO
            $product = Product::create($data);

            // ============================
            // ✅ 2. GUARDAR IMÁGENES (POLIMÓRFICO)
            // ============================
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $image) {

                    $path = $image->store('products/gallery', 'public');

                    // 🔥 CLAVE: RELACIÓN MORPH
                    $product->images()->create([
                        'image' => $path,
                        'order' => $index
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Producto creado correctamente.',
                'data'    => $product,
            ], 201);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Error creando producto: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al crear el producto.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('images');

        return response()->json([
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'category_id'       => $product->category_id,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'price'             => $product->price,
            'type'              => $product->type,
            'status'            => $product->status,

            // 🔥 IMÁGENES EXISTENTES
            'images' => $product->images->map(function ($img) {
                return [
                    'id' => $img->id,
                    'url' => asset('storage/' . $img->image)
                ];
            })
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'              => 'required|string|min:3|max:255',
            'slug'              => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id),
            ],
            'category_id'       => 'nullable|exists:categories,id',
            'short_description' => 'nullable|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',
            'type'              => 'required|in:sistema,servicio',
            'images.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'            => 'nullable|in:published',
            'images_delete.*'   => 'nullable|exists:images,id'
        ]);

        $data['status'] = $request->has('status') ? 'published' : 'draft';

        try {
            DB::beginTransaction();

            // 🔥 1. actualizar producto
            $product->update($data);

            // ============================
            // 🔥 2. ELIMINAR IMÁGENES
            // ============================
            if ($request->filled('images_delete')) {

                $images = $product->images()
                    ->whereIn('id', $request->images_delete)
                    ->get();

                foreach ($images as $img) {

                    if (Storage::disk('public')->exists($img->image)) {
                        Storage::disk('public')->delete($img->image);
                    }

                    $img->delete();
                }
            }

            // ============================
            // 🔥 3. AGREGAR NUEVAS IMÁGENES
            // ============================
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $index => $image) {

                    $path = $image->store('products/gallery', 'public');

                    $product->images()->create([
                        'image' => $path,
                        'order' => $index
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Producto actualizado correctamente.',
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Error actualizando producto: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al actualizar el producto.',
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function showView(Product $product)
    {
        $product->load(['category', 'images']); // 🔥 IMPORTANTE

        return response()->json([
            'id'                => $product->id,
            'name'              => $product->name,
            'slug'              => $product->slug,
            'short_description' => $product->short_description,
            'description'       => $product->description,
            'price'             => 'S/ ' . number_format($product->price, 2),
            'type'              => $product->type,
            'status'            => $product->status,
            'category'          => $product->category?->name ?? '—',
            'created_at'        => optional($product->created_at)->format('d/m/Y h:i A'),

            // 🔥 GALERÍA
            'images' => $product->images->map(function ($img) {
                return asset('storage/' . $img->image);
            }),
        ]);
    }

    /**
     * DELETE
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            // 🟡 SOLO CAMBIAMOS ESTADO
            $product->update([
                'status' => 'removed'
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Producto enviado a papelera.'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return response()->json([
                'status'  => 'error',
                'message' => 'El producto no existe.'
            ], 404);
        } catch (\Throwable $e) {

            Log::error('Error eliminando producto: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Error al eliminar el producto.'
            ], 500);
        }
    }
}
