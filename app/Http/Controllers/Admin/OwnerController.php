<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OwnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.owners.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.owners.store')->only('store');
        $this->middleware('can:admin.owners.update')->only('update');
        $this->middleware('can:admin.owners.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.owners.index');
    }

    public function list(): JsonResponse
    {
        $owners = Owner::query()->latest('id');

        return DataTables::eloquent($owners)
            ->addIndexColumn()
            ->editColumn('owner_type', fn (Owner $owner) => $owner->owner_type === 'company'
                ? '<span class="badge badge-info">Empresa</span>'
                : '<span class="badge badge-primary">Persona</span>')
            ->editColumn('document_number', function (Owner $owner) {
                if (! $owner->document_number) {
                    return '<span class="text-muted">—</span>';
                }

                return e(trim(($this->documentTypeLabel($owner->document_type).' ').$owner->document_number));
            })
            ->addColumn('display_name', fn (Owner $owner) => e(
                $owner->owner_type === 'company' && $owner->business_name
                    ? $owner->business_name
                    : $owner->full_name
            ))
            ->editColumn('status', fn (Owner $owner) => $owner->status === 'active'
                ? '<span class="badge badge-success">Activo</span>'
                : '<span class="badge badge-danger">Inactivo</span>')
            ->editColumn('created_at', fn (Owner $owner) => $owner->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Owner $owner) => view(
                'admin.owners.partials.acciones',
                compact('owner')
            )->render())
            ->rawColumns(['owner_type', 'document_number', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        Owner::create($this->validatedData($request));

        return response()->json([
            'message' => 'Propietario registrado correctamente.',
        ]);
    }

    public function show(Owner $owner): JsonResponse
    {
        return response()->json([
            'owner' => array_merge($owner->toArray(), [
                'owner_type_label' => $owner->owner_type === 'company' ? 'Empresa' : 'Persona',
                'document_type_label' => $this->documentTypeLabel($owner->document_type),
                'status_label' => $owner->status === 'active' ? 'Activo' : 'Inactivo',
                'created_at_formatted' => $owner->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $owner->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Owner $owner): JsonResponse
    {
        $owner->update($this->validatedData($request));

        return response()->json([
            'message' => 'Propietario actualizado correctamente.',
        ]);
    }

    public function destroy(Owner $owner): JsonResponse
    {
        $owner->delete();

        return response()->json([
            'message' => 'Propietario eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'owner_type' => ['required', 'in:person,company'],
            'document_type' => ['nullable', 'in:DNI,RUC,CE,PASSPORT,OTHER'],
            'document_number' => ['nullable', 'string', 'max:30'],
            'full_name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ], [
            'owner_type.required' => 'Seleccione el tipo de propietario.',
            'owner_type.in' => 'El tipo de propietario seleccionado no es válido.',
            'document_type.in' => 'El tipo de documento seleccionado no es válido.',
            'full_name.required' => 'El nombre completo o contacto es obligatorio.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'status.required' => 'Seleccione el estado del propietario.',
            'status.in' => 'El estado seleccionado no es válido.',
        ]);
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
