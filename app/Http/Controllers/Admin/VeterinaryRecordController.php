<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Veterinarian;
use App\Models\VeterinaryRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VeterinaryRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.veterinary-records.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.veterinary-records.store')->only('store');
        $this->middleware('can:admin.veterinary-records.update')->only('update');
        $this->middleware('can:admin.veterinary-records.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.veterinary_records.index', [
            'cattle' => Cattle::with(['breed', 'currentOwner'])
                ->where('status', 'active')
                ->orderBy('code')
                ->get(),
            'veterinarians' => Veterinarian::where('status', 'active')
                ->orderBy('full_name')
                ->get(),
        ]);
    }

    public function list(): JsonResponse
    {
        $records = VeterinaryRecord::query()
            ->with(['cattle.breed', 'veterinarian'])
            ->latest('id');

        return DataTables::eloquent($records)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (VeterinaryRecord $record) => $record->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (VeterinaryRecord $record) => $record->cattle?->code ?: '-')
            ->addColumn('veterinarian_name', fn (VeterinaryRecord $record) => $this->veterinarianLabel($record->veterinarian))
            ->editColumn('record_type', fn (VeterinaryRecord $record) => $this->recordTypeBadge($record->record_type))
            ->editColumn('record_date', fn (VeterinaryRecord $record) => $record->record_date?->format('d/m/Y') ?: '-')
            ->editColumn('next_visit_date', fn (VeterinaryRecord $record) => $record->next_visit_date?->format('d/m/Y') ?: '-')
            ->addColumn('document', fn (VeterinaryRecord $record) => $record->document_path
                ? '<span class="badge badge-success">Con archivo</span>'
                : '<span class="badge badge-secondary">Sin archivo</span>')
            ->editColumn('created_at', fn (VeterinaryRecord $record) => $record->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (VeterinaryRecord $record) => view(
                'admin.veterinary_records.partials.acciones',
                compact('record')
            )->render())
            ->rawColumns(['record_type', 'document', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPath = $this->storeDocument($request);

        try {
            VeterinaryRecord::create(array_merge($data, $uploadedPath));
        } catch (\Throwable $exception) {
            $this->deleteDocument($uploadedPath['document_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Revision veterinaria registrada correctamente.',
        ]);
    }

    public function show(VeterinaryRecord $veterinaryRecord): JsonResponse
    {
        $veterinaryRecord->load(['cattle.breed', 'cattle.currentOwner', 'veterinarian']);

        return response()->json([
            'record' => array_merge($veterinaryRecord->toArray(), [
                'cattle_label' => $this->cattleLabel($veterinaryRecord->cattle),
                'cattle_code' => $veterinaryRecord->cattle?->code,
                'cattle_name' => $veterinaryRecord->cattle?->name,
                'cattle_breed_name' => $veterinaryRecord->cattle?->breed?->name,
                'cattle_owner_name' => $this->ownerDisplayName($veterinaryRecord->cattle?->currentOwner),
                'cattle_photo_url' => $this->fileUrl($veterinaryRecord->cattle?->main_photo_path),
                'veterinarian_name' => $veterinaryRecord->veterinarian?->full_name,
                'veterinarian_license' => $veterinaryRecord->veterinarian?->license_number,
                'veterinarian_specialty' => $veterinaryRecord->veterinarian?->specialty,
                'record_type_label' => $this->recordTypeLabel($veterinaryRecord->record_type),
                'record_date' => $veterinaryRecord->record_date?->format('Y-m-d'),
                'next_visit_date' => $veterinaryRecord->next_visit_date?->format('Y-m-d'),
                'record_date_formatted' => $veterinaryRecord->record_date?->format('d/m/Y'),
                'next_visit_date_formatted' => $veterinaryRecord->next_visit_date?->format('d/m/Y') ?: 'Sin programar',
                'document_url' => $this->fileUrl($veterinaryRecord->document_path),
                'document_name' => $veterinaryRecord->document_path ? basename($veterinaryRecord->document_path) : null,
                'created_at_formatted' => $veterinaryRecord->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $veterinaryRecord->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, VeterinaryRecord $veterinaryRecord): JsonResponse
    {
        $data = $this->validatedData($request);
        $uploadedPath = $this->storeDocument($request);
        $oldDocumentPath = $uploadedPath ? $veterinaryRecord->document_path : null;

        try {
            $veterinaryRecord->update(array_merge($data, $uploadedPath));
        } catch (\Throwable $exception) {
            $this->deleteDocument($uploadedPath['document_path'] ?? null);
            throw $exception;
        }

        $this->deleteDocument($oldDocumentPath);

        return response()->json([
            'message' => 'Revision veterinaria actualizada correctamente.',
        ]);
    }

    public function destroy(VeterinaryRecord $veterinaryRecord): JsonResponse
    {
        $veterinaryRecord->delete();

        return response()->json([
            'message' => 'Revision veterinaria eliminada correctamente.',
        ]);
    }

    public function create() {}

    public function edit(VeterinaryRecord $veterinaryRecord) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'veterinarian_id' => ['nullable', 'exists:veterinarians,id'],
            'record_date' => ['required', 'date'],
            'record_type' => ['required', Rule::in(array_keys($this->recordTypes()))],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
            'next_visit_date' => ['nullable', 'date', 'after_or_equal:record_date'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'veterinarian_id.exists' => 'El veterinario seleccionado no es valido.',
            'record_date.required' => 'Ingrese la fecha de atencion.',
            'record_date.date' => 'La fecha de atencion no es valida.',
            'record_type.required' => 'Seleccione el tipo de revision.',
            'record_type.in' => 'El tipo de revision seleccionado no es valido.',
            'next_visit_date.date' => 'La proxima visita no es valida.',
            'next_visit_date.after_or_equal' => 'La proxima visita no puede ser anterior a la fecha de atencion.',
            'document_file.file' => 'El archivo debe ser PDF, imagen o Word y no superar los 5 MB.',
            'document_file.mimes' => 'El archivo debe ser PDF, imagen o Word y no superar los 5 MB.',
            'document_file.max' => 'El archivo debe ser PDF, imagen o Word y no superar los 5 MB.',
        ]);
    }

    private function storeDocument(Request $request): array
    {
        if (! $request->hasFile('document_file')) {
            return [];
        }

        return [
            'document_path' => $request->file('document_file')
                ->store('cattle/veterinary-records', 'public'),
        ];
    }

    private function deleteDocument(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function recordTypes(): array
    {
        return [
            'checkup' => 'Revision',
            'illness' => 'Enfermedad',
            'control' => 'Control',
            'certification' => 'Certificacion',
            'emergency' => 'Emergencia',
            'other' => 'Otro',
        ];
    }

    private function recordTypeLabel(?string $type): string
    {
        return $this->recordTypes()[$type] ?? '-';
    }

    private function recordTypeBadge(?string $type): string
    {
        $classes = [
            'checkup' => 'badge-success',
            'illness' => 'badge-warning',
            'control' => 'badge-primary',
            'certification' => 'badge-info',
            'emergency' => 'badge-danger',
            'other' => 'badge-secondary',
        ];

        return '<span class="badge '.($classes[$type] ?? 'badge-secondary').'">'.$this->recordTypeLabel($type).'</span>';
    }

    private function veterinarianLabel(?Veterinarian $veterinarian): string
    {
        if (! $veterinarian) {
            return '-';
        }

        return trim($veterinarian->full_name.($veterinarian->license_number ? ' - '.$veterinarian->license_number : ''));
    }

    private function cattleLabel(?Cattle $cattle): string
    {
        if (! $cattle) {
            return '-';
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function ownerDisplayName($owner): ?string
    {
        if (! $owner) {
            return null;
        }

        return $owner->owner_type === 'company' && $owner->business_name
            ? $owner->business_name
            : $owner->full_name;
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
