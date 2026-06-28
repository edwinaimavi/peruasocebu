<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\Treatment;
use App\Models\Veterinarian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TreatmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.treatments.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.treatments.store')->only('store');
        $this->middleware('can:admin.treatments.update')->only('update');
        $this->middleware('can:admin.treatments.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.treatments.index', [
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
        $treatments = Treatment::query()
            ->with(['cattle.breed', 'veterinarian'])
            ->latest('id');

        return DataTables::eloquent($treatments)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (Treatment $treatment) => $treatment->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (Treatment $treatment) => $treatment->cattle?->code ?: '-')
            ->editColumn('treatment_name', fn (Treatment $treatment) => $this->treatmentNameBadge($treatment))
            ->editColumn('medicine', fn (Treatment $treatment) => e($treatment->medicine ?: '-'))
            ->editColumn('dose', fn (Treatment $treatment) => e($treatment->dose ?: '-'))
            ->editColumn('duration', fn (Treatment $treatment) => $this->durationBadge($treatment->duration))
            ->editColumn('treatment_date', fn (Treatment $treatment) => $treatment->treatment_date?->format('d/m/Y') ?: '-')
            ->addColumn('veterinarian_name', fn (Treatment $treatment) => $this->veterinarianBadge($treatment->veterinarian))
            ->editColumn('created_at', fn (Treatment $treatment) => $treatment->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Treatment $treatment) => view(
                'admin.treatments.partials.acciones',
                compact('treatment')
            )->render())
            ->rawColumns(['treatment_name', 'duration', 'veterinarian_name', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        Treatment::create($this->validatedData($request));

        return response()->json([
            'message' => 'Tratamiento registrado correctamente.',
        ]);
    }

    public function show(Treatment $treatment): JsonResponse
    {
        $treatment->load(['cattle.breed', 'cattle.currentOwner', 'veterinarian']);

        return response()->json([
            'treatment' => array_merge($treatment->toArray(), [
                'cattle_label' => $this->cattleLabel($treatment->cattle),
                'cattle_code' => $treatment->cattle?->code,
                'cattle_name' => $treatment->cattle?->name,
                'cattle_breed_name' => $treatment->cattle?->breed?->name,
                'cattle_owner_name' => $this->ownerDisplayName($treatment->cattle?->currentOwner),
                'cattle_photo_url' => $this->mainPhotoUrl($treatment->cattle?->main_photo_path),
                'veterinarian_name' => $treatment->veterinarian?->full_name,
                'veterinarian_license' => $treatment->veterinarian?->license_number,
                'veterinarian_specialty' => $treatment->veterinarian?->specialty,
                'veterinarian_status_label' => $treatment->veterinarian ? 'Con veterinario' : 'Sin veterinario',
                'treatment_date' => $treatment->treatment_date?->format('Y-m-d'),
                'treatment_date_formatted' => $treatment->treatment_date?->format('d/m/Y'),
                'created_at_formatted' => $treatment->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $treatment->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Treatment $treatment): JsonResponse
    {
        $treatment->update($this->validatedData($request));

        return response()->json([
            'message' => 'Tratamiento actualizado correctamente.',
        ]);
    }

    public function destroy(Treatment $treatment): JsonResponse
    {
        $treatment->delete();

        return response()->json([
            'message' => 'Tratamiento eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'veterinarian_id' => ['nullable', 'exists:veterinarians,id'],
            'treatment_date' => ['required', 'date'],
            'treatment_name' => ['required', 'string', 'max:255'],
            'medicine' => ['nullable', 'string', 'max:255'],
            'dose' => ['nullable', 'string', 'max:100'],
            'duration' => ['nullable', 'string', 'max:100'],
            'reason' => ['nullable', 'string'],
            'observations' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'veterinarian_id.exists' => 'El veterinario seleccionado no es valido.',
            'treatment_date.required' => 'Ingrese la fecha del tratamiento.',
            'treatment_date.date' => 'La fecha del tratamiento no es valida.',
            'treatment_name.required' => 'Ingrese el nombre del tratamiento.',
            'treatment_name.max' => 'El nombre del tratamiento no debe superar 255 caracteres.',
            'medicine.max' => 'El medicamento no debe superar 255 caracteres.',
            'dose.max' => 'La dosis no debe superar 100 caracteres.',
            'duration.max' => 'La duracion no debe superar 100 caracteres.',
        ]);
    }

    private function treatmentNameBadge(Treatment $treatment): string
    {
        return '<span class="badge badge-success px-2 py-1">'.e($treatment->treatment_name).'</span>';
    }

    private function durationBadge(?string $duration): string
    {
        if (! $duration) {
            return '<span class="text-muted">-</span>';
        }

        return '<span class="badge badge-warning px-2 py-1">'.e($duration).'</span>';
    }

    private function veterinarianBadge(?Veterinarian $veterinarian): string
    {
        if (! $veterinarian) {
            return '<span class="badge badge-secondary">Sin veterinario</span>';
        }

        $label = trim($veterinarian->full_name.($veterinarian->license_number ? ' - '.$veterinarian->license_number : ''));

        return '<span class="badge badge-info">Con veterinario</span><div class="small mt-1">'.e($label).'</div>';
    }

    private function cattleLabel(?Cattle $cattle): string
    {
        if (! $cattle) {
            return '-';
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function ownerDisplayName(?Owner $owner): ?string
    {
        if (! $owner) {
            return null;
        }

        return $owner->owner_type === 'company' && $owner->business_name
            ? $owner->business_name
            : $owner->full_name;
    }

    private function mainPhotoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
