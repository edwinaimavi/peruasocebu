<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\WeightRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WeightRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.weight-records.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.weight-records.store')->only('store');
        $this->middleware('can:admin.weight-records.update')->only('update');
        $this->middleware('can:admin.weight-records.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.weight_records.index', [
            'cattle' => Cattle::with(['breed', 'currentOwner'])
                ->where('status', 'active')
                ->orderBy('code')
                ->get(),
            'bodyConditions' => $this->bodyConditions(),
        ]);
    }

    public function list(): JsonResponse
    {
        $records = WeightRecord::query()
            ->with(['cattle.breed', 'cattle.currentOwner'])
            ->latest('id');

        return DataTables::eloquent($records)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (WeightRecord $record) => $record->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (WeightRecord $record) => $record->cattle?->code ?: '-')
            ->editColumn('weight_kg', fn (WeightRecord $record) => $this->weightBadge($record->weight_kg))
            ->editColumn('record_date', fn (WeightRecord $record) => $record->record_date?->format('d/m/Y') ?: '-')
            ->editColumn('body_condition', fn (WeightRecord $record) => $this->bodyConditionBadge($record->body_condition))
            ->editColumn('created_at', fn (WeightRecord $record) => $record->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (WeightRecord $record) => view(
                'admin.weight_records.partials.acciones',
                compact('record')
            )->render())
            ->rawColumns(['weight_kg', 'body_condition', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureExactDuplicateDoesNotExist($data);

        DB::transaction(function () use ($data): void {
            $record = WeightRecord::create($data);
            $this->syncCurrentCattleWeight((int) $record->cattle_id);
        });

        return response()->json([
            'message' => 'Pesaje registrado correctamente.',
        ]);
    }

    public function show(WeightRecord $weightRecord): JsonResponse
    {
        $weightRecord->load(['cattle.breed', 'cattle.currentOwner']);

        $previousRecord = WeightRecord::query()
            ->where('cattle_id', $weightRecord->cattle_id)
            ->whereKeyNot($weightRecord->id)
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'record' => array_merge($weightRecord->toArray(), [
                'cattle_label' => $this->cattleLabel($weightRecord->cattle),
                'cattle_code' => $weightRecord->cattle?->code,
                'cattle_name' => $weightRecord->cattle?->name,
                'cattle_breed_name' => $weightRecord->cattle?->breed?->name,
                'cattle_owner_name' => $this->ownerDisplayName($weightRecord->cattle?->currentOwner),
                'cattle_photo_url' => $this->mainPhotoUrl($weightRecord->cattle?->main_photo_path),
                'weight_kg_formatted' => $this->formatWeight($weightRecord->weight_kg),
                'record_date' => $weightRecord->record_date?->format('Y-m-d'),
                'record_date_formatted' => $weightRecord->record_date?->format('d/m/Y'),
                'body_condition_label' => $weightRecord->body_condition ?: 'Sin dato',
                'previous_weight_kg' => $previousRecord?->weight_kg,
                'previous_weight_kg_formatted' => $previousRecord ? $this->formatWeight($previousRecord->weight_kg) : null,
                'weight_difference' => $previousRecord
                    ? number_format((float) $weightRecord->weight_kg - (float) $previousRecord->weight_kg, 2)
                    : null,
                'created_at_formatted' => $weightRecord->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $weightRecord->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, WeightRecord $weightRecord): JsonResponse
    {
        $oldCattleId = (int) $weightRecord->cattle_id;
        $data = $this->validatedData($request);
        $this->ensureExactDuplicateDoesNotExist($data, $weightRecord);

        DB::transaction(function () use ($weightRecord, $data, $oldCattleId): void {
            $weightRecord->update($data);
            $this->syncCurrentCattleWeight($oldCattleId);
            $this->syncCurrentCattleWeight((int) $weightRecord->cattle_id);
        });

        return response()->json([
            'message' => 'Pesaje actualizado correctamente.',
        ]);
    }

    public function destroy(WeightRecord $weightRecord): JsonResponse
    {
        $cattleId = (int) $weightRecord->cattle_id;

        DB::transaction(function () use ($weightRecord, $cattleId): void {
            $weightRecord->delete();
            $this->syncCurrentCattleWeight($cattleId);
        });

        return response()->json([
            'message' => 'Pesaje eliminado correctamente.',
        ]);
    }

    public function create() {}

    public function edit(WeightRecord $weightRecord) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'weight_kg' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'record_date' => ['required', 'date'],
            'body_condition' => ['nullable', 'string', 'max:100'],
            'observations' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'weight_kg.required' => 'Ingrese el peso en kilogramos.',
            'weight_kg.numeric' => 'El peso debe ser numerico.',
            'weight_kg.min' => 'El peso debe ser mayor a cero.',
            'weight_kg.max' => 'El peso no debe superar 9999.99 kg.',
            'record_date.required' => 'Ingrese la fecha del pesaje.',
            'record_date.date' => 'La fecha del pesaje no es valida.',
            'body_condition.max' => 'La condicion corporal no debe superar 100 caracteres.',
        ]);
    }

    private function ensureExactDuplicateDoesNotExist(array $data, ?WeightRecord $ignoreRecord = null): void
    {
        $exists = WeightRecord::query()
            ->where('cattle_id', $data['cattle_id'])
            ->whereDate('record_date', $data['record_date'])
            ->where('weight_kg', number_format((float) $data['weight_kg'], 2, '.', ''))
            ->when($ignoreRecord, fn ($query) => $query->whereKeyNot($ignoreRecord->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'weight_kg' => 'Ya existe un pesaje registrado para este ganado con la misma fecha y peso.',
            ]);
        }
    }

    private function syncCurrentCattleWeight(int $cattleId): void
    {
        $cattle = Cattle::find($cattleId);

        if (! $cattle) {
            return;
        }

        $latestRecord = WeightRecord::query()
            ->where('cattle_id', $cattleId)
            ->orderByDesc('record_date')
            ->orderByDesc('id')
            ->first();

        $cattle->weight_kg = $latestRecord?->weight_kg;
        $cattle->save();
    }

    private function bodyConditions(): array
    {
        return [
            'Excelente',
            'Buena',
            'Regular',
            'Baja',
            'Critica',
        ];
    }

    private function bodyConditionBadge(?string $condition): string
    {
        if (! $condition) {
            return '<span class="badge badge-secondary px-2 py-1">Sin dato</span>';
        }

        $key = strtolower($condition);
        $classes = [
            'excelente' => 'badge-success',
            'buena' => 'badge-info',
            'regular' => 'badge-warning',
            'baja' => 'badge-orange',
            'critica' => 'badge-danger',
        ];

        return '<span class="badge '.($classes[$key] ?? 'badge-secondary').' px-2 py-1">'.e($condition).'</span>';
    }

    private function weightBadge(null|string|float $weight): string
    {
        return '<span class="badge badge-light border px-2 py-1">'.e($this->formatWeight($weight)).'</span>';
    }

    private function formatWeight(null|string|float $weight): string
    {
        return $weight !== null ? number_format((float) $weight, 2).' kg' : '-';
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
