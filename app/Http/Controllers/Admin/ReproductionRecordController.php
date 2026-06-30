<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use App\Models\ReproductionRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ReproductionRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.reproduction-records.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.reproduction-records.store')->only('store');
        $this->middleware('can:admin.reproduction-records.update')->only('update');
        $this->middleware('can:admin.reproduction-records.destroy')->only('destroy');
    }

    public function index(): View
    {
        $cattle = Cattle::with(['breed', 'currentOwner'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('admin.reproduction_records.index', [
            'cattle' => $cattle,
            'femaleCattle' => $cattle->where('sex', 'female')->values(),
            'maleCattle' => $cattle->where('sex', 'male')->values(),
            'methods' => $this->methods(),
            'pregnancyResults' => $this->pregnancyResults(),
        ]);
    }

    public function list(): JsonResponse
    {
        $records = ReproductionRecord::query()
            ->with(['cattle.breed', 'partner.breed', 'offspring'])
            ->latest('id');

        return DataTables::eloquent($records)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (ReproductionRecord $record) => $record->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (ReproductionRecord $record) => $record->cattle?->code ?: '-')
            ->addColumn('partner_name', fn (ReproductionRecord $record) => $this->cattleLabel($record->partner, 'Sin pareja registrada'))
            ->editColumn('method', fn (ReproductionRecord $record) => $this->methodBadge($record->method))
            ->editColumn('reproduction_date', fn (ReproductionRecord $record) => $record->reproduction_date?->format('d/m/Y') ?: '-')
            ->editColumn('pregnancy_result', fn (ReproductionRecord $record) => $this->pregnancyResultBadge($record->pregnancy_result))
            ->editColumn('birth_date', fn (ReproductionRecord $record) => $this->birthBadge($record->birth_date))
            ->addColumn('offspring_name', fn (ReproductionRecord $record) => $this->cattleLabel($record->offspring, 'Sin cria vinculada'))
            ->editColumn('created_at', fn (ReproductionRecord $record) => $record->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (ReproductionRecord $record) => view(
                'admin.reproduction_records.partials.acciones',
                compact('record')
            )->render())
            ->rawColumns(['method', 'pregnancy_result', 'birth_date', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureReproductionDataIsValid($data);

        DB::transaction(function () use ($data): void {
            $record = ReproductionRecord::create($data);
            $this->syncOffspringGenealogy($record);
        });

        return response()->json([
            'message' => 'Registro reproductivo guardado correctamente.',
        ]);
    }

    public function show(ReproductionRecord $reproductionRecord): JsonResponse
    {
        $reproductionRecord->load([
            'cattle.breed',
            'cattle.currentOwner',
            'partner.breed',
            'partner.currentOwner',
            'offspring.breed',
        ]);

        return response()->json([
            'record' => array_merge($reproductionRecord->toArray(), [
                'cattle_label' => $this->cattleLabel($reproductionRecord->cattle),
                'cattle_code' => $reproductionRecord->cattle?->code,
                'cattle_name' => $reproductionRecord->cattle?->name,
                'cattle_sex_label' => $this->sexLabel($reproductionRecord->cattle?->sex),
                'cattle_breed_name' => $reproductionRecord->cattle?->breed?->name,
                'cattle_owner_name' => $this->ownerDisplayName($reproductionRecord->cattle?->currentOwner),
                'cattle_photo_url' => $this->mainPhotoUrl($reproductionRecord->cattle?->main_photo_path),
                'partner_label' => $this->cattleLabel($reproductionRecord->partner, 'Sin pareja registrada'),
                'partner_code' => $reproductionRecord->partner?->code,
                'partner_name' => $reproductionRecord->partner?->name,
                'partner_sex_label' => $this->sexLabel($reproductionRecord->partner?->sex),
                'partner_breed_name' => $reproductionRecord->partner?->breed?->name,
                'offspring_label' => $this->cattleLabel($reproductionRecord->offspring, 'Sin cria vinculada'),
                'method_label' => $this->methodLabel($reproductionRecord->method),
                'pregnancy_result_label' => $this->pregnancyResultLabel($reproductionRecord->pregnancy_result),
                'reproduction_date' => $reproductionRecord->reproduction_date?->format('Y-m-d'),
                'pregnancy_check_date' => $reproductionRecord->pregnancy_check_date?->format('Y-m-d'),
                'birth_date' => $reproductionRecord->birth_date?->format('Y-m-d'),
                'reproduction_date_formatted' => $reproductionRecord->reproduction_date?->format('d/m/Y'),
                'pregnancy_check_date_formatted' => $reproductionRecord->pregnancy_check_date?->format('d/m/Y') ?: 'Sin control',
                'birth_date_formatted' => $reproductionRecord->birth_date?->format('d/m/Y') ?: 'Sin parto',
                'created_at_formatted' => $reproductionRecord->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $reproductionRecord->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, ReproductionRecord $reproductionRecord): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureReproductionDataIsValid($data, $reproductionRecord);

        DB::transaction(function () use ($reproductionRecord, $data): void {
            $reproductionRecord->update($data);
            $this->syncOffspringGenealogy($reproductionRecord->refresh());
        });

        return response()->json([
            'message' => 'Registro reproductivo actualizado correctamente.',
        ]);
    }

    public function destroy(ReproductionRecord $reproductionRecord): JsonResponse
    {
        $reproductionRecord->delete();

        return response()->json([
            'message' => 'Registro reproductivo eliminado correctamente.',
        ]);
    }

    public function create() {}

    public function edit(ReproductionRecord $reproductionRecord) {}

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'partner_cattle_id' => ['nullable', 'exists:cattle,id', 'different:cattle_id'],
            'method' => ['required', Rule::in(array_keys($this->methods()))],
            'reproduction_date' => ['required', 'date'],
            'pregnancy_check_date' => ['nullable', 'date', 'after_or_equal:reproduction_date'],
            'pregnancy_result' => ['required', Rule::in(array_keys($this->pregnancyResults()))],
            'birth_date' => ['nullable', 'date', 'after_or_equal:reproduction_date'],
            'offspring_cattle_id' => ['nullable', 'exists:cattle,id', 'different:cattle_id', 'different:partner_cattle_id'],
            'observations' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el animal principal.',
            'cattle_id.exists' => 'El animal principal seleccionado no es valido.',
            'partner_cattle_id.exists' => 'La pareja seleccionada no es valida.',
            'partner_cattle_id.different' => 'El animal principal y la pareja no pueden ser el mismo.',
            'method.required' => 'Seleccione el metodo reproductivo.',
            'method.in' => 'El metodo reproductivo seleccionado no es valido.',
            'reproduction_date.required' => 'Ingrese la fecha reproductiva.',
            'reproduction_date.date' => 'La fecha reproductiva no es valida.',
            'pregnancy_check_date.date' => 'La fecha de control de prenez no es valida.',
            'pregnancy_check_date.after_or_equal' => 'La fecha de control de prenez no puede ser menor que la fecha reproductiva.',
            'pregnancy_result.required' => 'Seleccione el resultado de prenez.',
            'pregnancy_result.in' => 'El resultado de prenez seleccionado no es valido.',
            'birth_date.date' => 'La fecha de parto no es valida.',
            'birth_date.after_or_equal' => 'La fecha de parto no puede ser menor que la fecha reproductiva.',
            'offspring_cattle_id.exists' => 'La cria seleccionada no es valida.',
            'offspring_cattle_id.different' => 'La cria no puede ser igual a los padres.',
        ]);
    }

    private function ensureReproductionDataIsValid(array $data, ?ReproductionRecord $record = null): void
    {
        $this->ensureSexRulesAreValid($data);
        $this->ensurePregnancyRulesAreValid($data);
        $this->ensureOffspringGenealogyCanBeSynced($data);
    }

    private function ensureSexRulesAreValid(array $data): void
    {
        $cattle = Cattle::find($data['cattle_id']);

        if ($cattle && $cattle->sex !== 'female') {
            throw ValidationException::withMessages([
                'cattle_id' => 'El animal principal del registro reproductivo debe ser una hembra.',
            ]);
        }

        if (empty($data['partner_cattle_id'])) {
            return;
        }

        $partner = Cattle::find($data['partner_cattle_id']);

        if ($partner && $partner->sex !== 'male') {
            throw ValidationException::withMessages([
                'partner_cattle_id' => 'La pareja seleccionada debe ser un macho.',
            ]);
        }
    }

    private function ensurePregnancyRulesAreValid(array $data): void
    {
        if (($data['pregnancy_result'] ?? null) === 'negative' && (! empty($data['birth_date']) || ! empty($data['offspring_cattle_id']))) {
            throw ValidationException::withMessages([
                'pregnancy_result' => 'Si el resultado es negativo, no puede registrar parto ni cria.',
            ]);
        }

        if (! empty($data['offspring_cattle_id']) && empty($data['birth_date'])) {
            throw ValidationException::withMessages([
                'birth_date' => 'Para registrar una cria nacida debe ingresar la fecha de parto.',
            ]);
        }

        if (! empty($data['birth_date']) && ($data['pregnancy_result'] ?? null) !== 'positive') {
            throw ValidationException::withMessages([
                'pregnancy_result' => 'Para registrar parto el resultado de prenez debe ser positivo.',
            ]);
        }
    }

    private function ensureOffspringGenealogyCanBeSynced(array $data): void
    {
        if (empty($data['offspring_cattle_id'])) {
            return;
        }

        $offspring = Cattle::find($data['offspring_cattle_id']);

        if (! $offspring) {
            return;
        }

        $motherId = (int) $data['cattle_id'];
        $fatherId = ! empty($data['partner_cattle_id']) ? (int) $data['partner_cattle_id'] : null;

        if ($offspring->mother_id && (int) $offspring->mother_id !== $motherId) {
            throw ValidationException::withMessages([
                'offspring_cattle_id' => 'La cria ya tiene una madre registrada.',
            ]);
        }

        if ($fatherId && $offspring->father_id && (int) $offspring->father_id !== $fatherId) {
            throw ValidationException::withMessages([
                'offspring_cattle_id' => 'La cria ya tiene un padre registrado.',
            ]);
        }

        foreach ([['mother', $motherId], ['father', $fatherId]] as [$relation, $relativeId]) {
            if (! $relativeId) {
                continue;
            }

            $lineagePath = $relation === 'father' ? 'F' : 'M';
            $existing = CattleGenealogyLink::query()
                ->where('cattle_id', $offspring->id)
                ->where(function ($query) use ($relation, $lineagePath) {
                    $query->where('lineage_path', $lineagePath)
                        ->orWhere(function ($legacyQuery) use ($relation) {
                            $legacyQuery->whereNull('lineage_path')->where('relation_type', $relation);
                        });
                })
                ->first();

            if ($existing && (int) $existing->relative_cattle_id !== $relativeId) {
                throw ValidationException::withMessages([
                    'offspring_cattle_id' => $relation === 'mother'
                        ? 'La cria ya tiene una madre registrada en genealogia.'
                        : 'La cria ya tiene un padre registrado en genealogia.',
                ]);
            }
        }
    }

    private function syncOffspringGenealogy(ReproductionRecord $record): void
    {
        if (! $record->offspring_cattle_id) {
            return;
        }

        $record->loadMissing(['cattle', 'partner', 'offspring']);

        $offspring = $record->offspring;

        if (! $offspring) {
            return;
        }

        $offspring->mother_id = $record->cattle_id;

        if ($record->partner_cattle_id) {
            $offspring->father_id = $record->partner_cattle_id;
        }

        $offspring->save();

        $this->syncParentGenealogyLink($offspring, $record->cattle, 'mother');

        if ($record->partner) {
            $this->syncParentGenealogyLink($offspring, $record->partner, 'father');
        }
    }

    private function syncParentGenealogyLink(Cattle $offspring, ?Cattle $parent, string $relation): void
    {
        if (! $parent) {
            return;
        }

        $lineagePath = $relation === 'father' ? 'F' : 'M';

        CattleGenealogyLink::updateOrCreate(
            [
                'cattle_id' => $offspring->id,
                'lineage_path' => $lineagePath,
            ],
            [
                'relation_type' => $relation,
                'relative_cattle_id' => $parent->id,
                'lineage_path' => $lineagePath,
                'generation_level' => 1,
                'relative_code' => $parent->code,
                'relative_name' => $parent->name,
                'breed_id' => $parent->breed_id,
                'purity_percentage' => $parent->purity_percentage,
            ]
        );
    }

    private function methods(): array
    {
        return [
            'natural_mating' => 'Monta natural',
            'artificial_insemination' => 'Inseminacion artificial',
            'embryo_transfer' => 'Transferencia embrionaria',
        ];
    }

    private function pregnancyResults(): array
    {
        return [
            'positive' => 'Positivo',
            'negative' => 'Negativo',
            'pending' => 'Pendiente',
        ];
    }

    private function methodLabel(?string $method): string
    {
        return $this->methods()[$method] ?? '-';
    }

    private function pregnancyResultLabel(?string $result): string
    {
        return $this->pregnancyResults()[$result] ?? '-';
    }

    private function methodBadge(?string $method): string
    {
        $classes = [
            'natural_mating' => 'badge-success',
            'artificial_insemination' => 'badge-info',
            'embryo_transfer' => 'badge-warning',
        ];

        return '<span class="badge '.($classes[$method] ?? 'badge-secondary').' px-2 py-1">'.$this->methodLabel($method).'</span>';
    }

    private function pregnancyResultBadge(?string $result): string
    {
        $classes = [
            'positive' => 'badge-success',
            'negative' => 'badge-danger',
            'pending' => 'badge-warning',
        ];

        return '<span class="badge '.($classes[$result] ?? 'badge-secondary').' px-2 py-1">'.$this->pregnancyResultLabel($result).'</span>';
    }

    private function birthBadge($date): string
    {
        if (! $date) {
            return '<span class="badge badge-secondary px-2 py-1">Sin parto</span>';
        }

        return '<span class="badge badge-success px-2 py-1">Parto registrado</span><div class="small mt-1">'.e($date->format('d/m/Y')).'</div>';
    }

    private function cattleLabel(?Cattle $cattle, string $empty = '-'): string
    {
        if (! $cattle) {
            return $empty;
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function sexLabel(?string $sex): string
    {
        return match ($sex) {
            'male' => 'Macho',
            'female' => 'Hembra',
            default => '-',
        };
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
