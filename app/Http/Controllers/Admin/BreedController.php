<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->rawColumns(['code', 'name', 'origin_country', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $data['code'] = $this->generateBreedCode($data['name']);

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

        $breed->update($data);

        return response()->json([
            'message' => 'Raza actualizada correctamente.',
        ]);
    }

    public function destroy(Breed $breed): JsonResponse
    {
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
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'El nombre de la raza es obligatorio.',
            'code.required' => 'El código de la raza es obligatorio.',
            'code.regex' => 'El código no debe contener espacios ni caracteres especiales.',
            'code.unique' => 'El código de la raza ya está registrado.',
            'status.required' => 'Seleccione el estado de la raza.',
            'status.in' => 'El estado seleccionado no es válido.',
        ]);

        return $data;
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
