<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Vaccination;
use App\Models\Veterinarian;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class VaccinationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.vaccinations.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.vaccinations.store')->only('store');
        $this->middleware('can:admin.vaccinations.update')->only('update');
        $this->middleware('can:admin.vaccinations.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.vaccinations.index', [
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
        $vaccinations = Vaccination::query()
            ->with(['cattle.breed', 'veterinarian'])
            ->latest('id');

        return DataTables::eloquent($vaccinations)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (Vaccination $vaccination) => $vaccination->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (Vaccination $vaccination) => $vaccination->cattle?->code ?: '-')
            ->editColumn('vaccine_name', fn (Vaccination $vaccination) => e($vaccination->vaccine_name))
            ->editColumn('application_date', fn (Vaccination $vaccination) => $vaccination->application_date?->format('d/m/Y') ?: '-')
            ->editColumn('next_due_date', fn (Vaccination $vaccination) => $this->nextDueLabel($vaccination))
            ->addColumn('veterinarian_name', fn (Vaccination $vaccination) => $this->veterinarianLabel($vaccination->veterinarian))
            ->editColumn('created_at', fn (Vaccination $vaccination) => $vaccination->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Vaccination $vaccination) => view(
                'admin.vaccinations.partials.acciones',
                compact('vaccination')
            )->render())
            ->rawColumns(['next_due_date', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        Vaccination::create($this->validatedData($request));

        return response()->json([
            'message' => 'Vacuna registrada correctamente.',
        ]);
    }

    public function show(Vaccination $vaccination): JsonResponse
    {
        $vaccination->load(['cattle.breed', 'cattle.currentOwner', 'veterinarian']);

        return response()->json([
            'vaccination' => array_merge($vaccination->toArray(), [
                'cattle_label' => $this->cattleLabel($vaccination->cattle),
                'cattle_code' => $vaccination->cattle?->code,
                'cattle_name' => $vaccination->cattle?->name,
                'cattle_breed_name' => $vaccination->cattle?->breed?->name,
                'cattle_owner_name' => $this->ownerDisplayName($vaccination->cattle?->currentOwner),
                'cattle_photo_url' => $vaccination->cattle?->main_photo_path
                    ? asset('storage/'.$vaccination->cattle->main_photo_path)
                    : null,
                'veterinarian_name' => $vaccination->veterinarian?->full_name,
                'veterinarian_license' => $vaccination->veterinarian?->license_number,
                'application_date' => $vaccination->application_date?->format('Y-m-d'),
                'next_due_date' => $vaccination->next_due_date?->format('Y-m-d'),
                'application_date_formatted' => $vaccination->application_date?->format('d/m/Y'),
                'next_due_date_formatted' => $vaccination->next_due_date?->format('d/m/Y') ?: 'No programada',
                'next_due_status' => $this->nextDueStatus($vaccination),
                'next_due_status_label' => $this->nextDueStatusLabel($vaccination),
                'created_at_formatted' => $vaccination->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $vaccination->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Vaccination $vaccination): JsonResponse
    {
        $vaccination->update($this->validatedData($request));

        return response()->json([
            'message' => 'Vacuna actualizada correctamente.',
        ]);
    }

    public function destroy(Vaccination $vaccination): JsonResponse
    {
        $vaccination->delete();

        return response()->json([
            'message' => 'Vacuna eliminada correctamente.',
        ]);
    }

    public function create() {}

    public function edit(Vaccination $vaccination) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'veterinarian_id' => ['nullable', 'exists:veterinarians,id'],
            'vaccine_name' => ['required', 'string', 'max:255'],
            'dose' => ['nullable', 'string', 'max:100'],
            'batch_number' => ['nullable', 'string', 'max:100'],
            'application_date' => ['required', 'date'],
            'next_due_date' => ['nullable', 'date', 'after_or_equal:application_date'],
            'observations' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'veterinarian_id.exists' => 'El veterinario seleccionado no es valido.',
            'vaccine_name.required' => 'Ingrese el nombre de la vacuna.',
            'vaccine_name.max' => 'El nombre de la vacuna no debe superar 255 caracteres.',
            'application_date.required' => 'Ingrese la fecha de aplicacion.',
            'application_date.date' => 'La fecha de aplicacion no es valida.',
            'next_due_date.date' => 'La proxima dosis no es valida.',
            'next_due_date.after_or_equal' => 'La proxima dosis no puede ser anterior a la fecha aplicada.',
        ]);
    }

    private function nextDueLabel(Vaccination $vaccination): string
    {
        $date = $vaccination->next_due_date?->format('d/m/Y') ?: 'No programada';

        return '<div>'.$date.'</div>'.$this->nextDueBadge($vaccination);
    }

    private function nextDueBadge(Vaccination $vaccination): string
    {
        $status = $this->nextDueStatus($vaccination);
        $classes = [
            'none' => 'badge-secondary',
            'scheduled' => 'badge-info',
            'today' => 'badge-warning',
            'overdue' => 'badge-danger',
        ];

        return '<span class="badge '.($classes[$status] ?? 'badge-secondary').'">'.$this->nextDueStatusLabel($vaccination).'</span>';
    }

    private function nextDueStatus(Vaccination $vaccination): string
    {
        if (! $vaccination->next_due_date) {
            return 'none';
        }

        $today = now()->startOfDay();
        $nextDue = Carbon::parse($vaccination->next_due_date)->startOfDay();

        if ($nextDue->isSameDay($today)) {
            return 'today';
        }

        return $nextDue->isPast() ? 'overdue' : 'scheduled';
    }

    private function nextDueStatusLabel(Vaccination $vaccination): string
    {
        return match ($this->nextDueStatus($vaccination)) {
            'scheduled' => 'Programada',
            'today' => 'Aplicar hoy',
            'overdue' => 'Vencida',
            default => 'Sin proxima dosis',
        };
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
}
