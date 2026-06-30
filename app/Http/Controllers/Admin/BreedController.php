<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BreedController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.breeds.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.breeds.store')->only('store');
        $this->middleware('can:admin.breeds.update')->only('update');
        $this->middleware('can:admin.breeds.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.breeds.index');
    }

    public function list(): JsonResponse
    {
        $breeds = Breed::query()->latest('id');

        return DataTables::eloquent($breeds)
            ->addIndexColumn()
            ->addColumn('image', fn (Breed $breed) => $this->tableImage($breed))
            ->editColumn('code', fn (Breed $breed) => '<span class="badge badge-light border px-2 py-1">'.e($breed->code).'</span>')
            ->editColumn('origin_country', fn (Breed $breed) => $breed->origin_country ?: '—')
            ->editColumn('status', fn (Breed $breed) => $breed->status === 'active'
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>')
            ->editColumn('created_at', fn (Breed $breed) => $breed->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Breed $breed) => view(
                'admin.breeds.partials.acciones',
                compact('breed')
            )->render())
            ->rawColumns(['image', 'code', 'name', 'origin_country', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $data['code'] = $this->generateBreedCode($data['name']);
        $data['image_path'] = $this->storeImage($request);

        Breed::create($data);

        return response()->json([
            'message' => 'Raza registrada correctamente.',
        ]);
    }

    public function show(Breed $breed): JsonResponse
    {
        return response()->json([
            'breed' => array_merge($breed->toArray(), [
                'status_label' => $breed->status === 'active' ? 'Activo' : 'Inactivo',
                'image_url' => $this->imageUrl($breed->image_path),
                'created_at_formatted' => $breed->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $breed->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Breed $breed): JsonResponse
    {
        $data = $this->validatedData($request, $breed);

        if ($breed->name !== $data['name']) {
            $data['code'] = $this->generateBreedCode($data['name'], $breed->id);
        } else {
            $data['code'] = $breed->code ?: $this->generateBreedCode($data['name'], $breed->id);
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($breed->image_path);
            $data['image_path'] = $this->storeImage($request);
        }

        $breed->update($data);

        return response()->json([
            'message' => 'Raza actualizada correctamente.',
        ]);
    }

    public function destroy(Breed $breed): JsonResponse
    {
        $this->deleteImage($breed->image_path);
        $breed->delete();

        return response()->json([
            'message' => 'Raza eliminada correctamente.',
        ]);
    }

    private function validatedData(Request $request, ?Breed $breed = null): array
    {
        if ($request->filled('code')) {
            $request->merge([
                'code' => strtoupper(trim((string) $request->input('code'))),
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'nullable',
                'string',
                'max:30',
            ],
            'description' => ['nullable', 'string'],
            'origin_country' => ['nullable', 'string', 'max:150'],
            'characteristics' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'El nombre de la raza es obligatorio.',
            'code.required' => 'El código de la raza es obligatorio.',
            'code.regex' => 'El código no debe contener espacios ni caracteres especiales.',
            'code.unique' => 'El código de la raza ya está registrado.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe estar en formato JPG, JPEG, PNG o WEBP.',
            'image.max' => 'La imagen no debe superar los 4 MB.',
            'status.required' => 'Seleccione el estado de la raza.',
            'status.in' => 'El estado seleccionado no es válido.',
        ]);

        unset($data['image']);
        $data['description'] = $this->sanitizeRichText($data['description'] ?? null);
        $data['characteristics'] = $this->sanitizeRichText($data['characteristics'] ?? null);

        return $data;
    }

    private function sanitizeRichText(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content) ?? '';
        $content = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $content) ?? '';
        $content = preg_replace('/\son\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $content) ?? '';
        $content = preg_replace('/javascript\s*:/is', '', $content) ?? '';

        return $content;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('breeds', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function imageUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function tableImage(Breed $breed): string
    {
        $imageUrl = $this->imageUrl($breed->image_path);

        if ($imageUrl) {
            return '<img class="breed-table-thumb" src="'.e($imageUrl).'" alt="Imagen de '.e($breed->name).'">';
        }

        return '<span class="breed-table-thumb-placeholder"><i class="fas fa-cow"></i></span>';
    }

    private function generateBreedCode(string $name, ?int $ignoreId = null): string
    {
        $prefix = $this->breedCodePrefix($name);

        for ($number = 1; $number <= 999; $number++) {
            $code = $prefix.str_pad((string) $number, 3, '0', STR_PAD_LEFT);

            $exists = Breed::query()
                ->where('code', $code)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        throw ValidationException::withMessages([
            'code' => 'No se pudo generar un código disponible para esta raza. Intente con otro nombre.',
        ]);
    }

    private function breedCodePrefix(string $name): string
    {
        $normalized = preg_replace('/[^A-Z]/', '', strtoupper(Str::ascii($name))) ?? '';

        return str_pad(substr($normalized, 0, 2), 2, 'X');
    }
}
