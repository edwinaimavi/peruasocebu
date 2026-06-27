<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veterinarian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VeterinarianController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.veterinarians.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.veterinarians.store')->only('store');
        $this->middleware('can:admin.veterinarians.update')->only('update');
        $this->middleware('can:admin.veterinarians.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.veterinarians.index');
    }

    public function list(): JsonResponse
    {
        $veterinarians = Veterinarian::query()->latest('id');

        return DataTables::eloquent($veterinarians)
            ->addIndexColumn()
            ->editColumn('document_number', function (Veterinarian $veterinarian) {
                if (! $veterinarian->document_number) {
                    return '<span class="text-muted">—</span>';
                }

                return e(trim(($this->documentTypeLabel($veterinarian->document_type).' ').$veterinarian->document_number));
            })
            ->editColumn('license_number', fn (Veterinarian $veterinarian) => e($veterinarian->license_number ?: '—'))
            ->editColumn('specialty', fn (Veterinarian $veterinarian) => e($veterinarian->specialty ?: '—'))
            ->editColumn('phone', fn (Veterinarian $veterinarian) => e($veterinarian->phone ?: '—'))
            ->editColumn('email', fn (Veterinarian $veterinarian) => e($veterinarian->email ?: '—'))
            ->editColumn('status', fn (Veterinarian $veterinarian) => $veterinarian->status === 'active'
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>')
            ->editColumn('created_at', fn (Veterinarian $veterinarian) => $veterinarian->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Veterinarian $veterinarian) => view(
                'admin.veterinarians.partials.acciones',
                compact('veterinarian')
            )->render())
            ->rawColumns(['document_number', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPath = $this->storeSignature($request);

        try {
            Veterinarian::create(array_merge($data, $uploadedPath));
        } catch (\Throwable $exception) {
            $this->deleteSignature($uploadedPath['signature_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Veterinario registrado correctamente.',
        ]);
    }

    public function show(Veterinarian $veterinarian): JsonResponse
    {
        return response()->json([
            'veterinarian' => array_merge($veterinarian->toArray(), [
                'document_type_label' => $this->documentTypeLabel($veterinarian->document_type),
                'signature_url' => $this->signatureUrl($veterinarian->signature_path),
                'status_label' => $veterinarian->status === 'active' ? 'Activo' : 'Inactivo',
                'created_at_formatted' => $veterinarian->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $veterinarian->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Veterinarian $veterinarian): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPath = $this->storeSignature($request);
        $oldSignaturePath = $uploadedPath ? $veterinarian->signature_path : null;

        try {
            $veterinarian->update(array_merge($data, $uploadedPath));
        } catch (\Throwable $exception) {
            $this->deleteSignature($uploadedPath['signature_path'] ?? null);
            throw $exception;
        }

        $this->deleteSignature($oldSignaturePath);

        return response()->json([
            'message' => 'Veterinario actualizado correctamente.',
        ]);
    }

    public function destroy(Veterinarian $veterinarian): JsonResponse
    {
        $veterinarian->delete();

        return response()->json([
            'message' => 'Veterinario eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request): array
    {
        $documentType = $request->input('document_type');

        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'document_type' => ['nullable', 'in:DNI,RUC,CE,PASSPORT,OTHER'],
            'document_number' => [
                'nullable',
                'string',
                'max:30',
                Rule::when($documentType === 'DNI', ['digits:8']),
                Rule::when($documentType === 'RUC', ['digits:11', 'starts_with:10']),
            ],
            'license_number' => ['nullable', 'string', 'max:100'],
            'specialty' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'full_name.required' => 'El nombre completo es obligatorio.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'document_number.digits' => $documentType === 'DNI'
                ? 'El DNI debe tener 8 dígitos.'
                : 'El RUC debe tener 11 dígitos.',
            'document_number.starts_with' => 'El RUC del veterinario debe ser de persona natural y empezar con 10.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'signature.image' => 'La firma debe ser una imagen.',
            'signature.mimes' => 'La firma debe ser JPG, PNG o WEBP.',
            'signature.max' => 'La firma no debe superar los 4 MB.',
            'status.required' => 'Seleccione el estado del veterinario.',
            'status.in' => 'El estado seleccionado no es válido.',
        ]);
    }

    private function storeSignature(Request $request): array
    {
        if (! $request->hasFile('signature')) {
            return [];
        }

        return [
            'signature_path' => $request->file('signature')->store('veterinarians/signatures', 'public'),
        ];
    }

    private function deleteSignature(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function signatureUrl(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }

    private function documentTypeLabel(?string $documentType): string
    {
        return match ($documentType) {
            'PASSPORT' => 'Pasaporte',
            'OTHER' => 'Otro',
            default => $documentType ?? '',
        };
    }
}
