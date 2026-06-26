<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RanchController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.ranches.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.ranches.store')->only('store');
        $this->middleware('can:admin.ranches.update')->only('update');
        $this->middleware('can:admin.ranches.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.ranches.index');
    }

    public function list(): JsonResponse
    {
        $ranches = Ranch::query()->latest('id');

        return DataTables::eloquent($ranches)
            ->addIndexColumn()
            ->editColumn('document_number', function (Ranch $ranch) {
                if (! $ranch->document_number) {
                    return '<span class="text-muted">—</span>';
                }

                return e(trim(($ranch->document_type ? $ranch->document_type.' ' : '').$ranch->document_number));
            })
            ->editColumn('status', fn (Ranch $ranch) => $ranch->status === 'active'
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>')
            ->editColumn('created_at', fn (Ranch $ranch) => $ranch->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Ranch $ranch) => view(
                'admin.ranches.partials.acciones',
                compact('ranch')
            )->render())
            ->rawColumns(['document_number', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPaths = $this->storeUploads($request);

        try {
            Ranch::create(array_merge($data, $uploadedPaths));
        } catch (\Throwable $exception) {
            $this->deleteFiles($uploadedPaths);
            throw $exception;
        }

        return response()->json([
            'message' => 'Criadero registrado correctamente.',
        ]);
    }

    public function show(Ranch $ranch): JsonResponse
    {
        return response()->json([
            'ranch' => array_merge($ranch->toArray(), [
                'logo_url' => $this->fileUrl($ranch->logo_path),
                'seal_url' => $this->fileUrl($ranch->seal_path),
                'signature_url' => $this->fileUrl($ranch->signature_path),
                'status_label' => $ranch->status === 'active' ? 'Activo' : 'Inactivo',
                'created_at_formatted' => $ranch->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $ranch->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Ranch $ranch): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPaths = $this->storeUploads($request);
        $oldPaths = [];

        foreach ($uploadedPaths as $attribute => $path) {
            if ($ranch->{$attribute}) {
                $oldPaths[$attribute] = $ranch->{$attribute};
            }
        }

        try {
            $ranch->update(array_merge($data, $uploadedPaths));
        } catch (\Throwable $exception) {
            $this->deleteFiles($uploadedPaths);
            throw $exception;
        }

        $this->deleteFiles($oldPaths);

        return response()->json([
            'message' => 'Criadero actualizado correctamente.',
        ]);
    }

    public function destroy(Ranch $ranch): JsonResponse
    {
        $ranch->delete();

        return response()->json([
            'message' => 'Criadero eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'document_type' => ['nullable', 'in:DNI,RUC,CE,Otro'],
            'document_number' => [
                'nullable',
                'string',
                'max:30',
                Rule::when($request->input('document_type') === 'DNI', ['regex:/^\d{8}$/']),
                Rule::when($request->input('document_type') === 'RUC', ['regex:/^\d{11}$/']),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'representative_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'seal' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'name.required' => 'El nombre del criadero es obligatorio.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'document_number.regex' => $request->input('document_type') === 'DNI'
                ? 'El DNI debe tener 8 dígitos.'
                : 'El RUC debe tener 11 dígitos.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'logo.image' => 'El logo debe ser una imagen.',
            'seal.image' => 'El sello debe ser una imagen.',
            'signature.image' => 'La firma debe ser una imagen.',
            '*.mimes' => 'El archivo debe ser JPG, PNG o WEBP.',
            '*.max' => 'Cada archivo puede pesar como máximo 4 MB.',
            'status.required' => 'Seleccione el estado del criadero.',
        ]);
    }

    private function storeUploads(Request $request): array
    {
        $paths = [];
        $uploads = [
            'logo' => ['attribute' => 'logo_path', 'directory' => 'ranches/logos'],
            'seal' => ['attribute' => 'seal_path', 'directory' => 'ranches/seals'],
            'signature' => ['attribute' => 'signature_path', 'directory' => 'ranches/signatures'],
        ];

        foreach ($uploads as $input => $config) {
            if ($request->hasFile($input)) {
                $paths[$config['attribute']] = $request->file($input)->store(
                    $config['directory'],
                    'public'
                );
            }
        }

        return $paths;
    }

    private function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
