<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CattleGenealogyLinkController extends Controller
{
    private const RELATION_TYPES = [
        'father',
        'mother',
        'paternal_grandfather',
        'paternal_grandmother',
        'maternal_grandfather',
        'maternal_grandmother',
    ];

    public function __construct()
    {
        $this->middleware('can:admin.cattle-genealogy.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.cattle-genealogy.store')->only('store');
        $this->middleware('can:admin.cattle-genealogy.update')->only('update');
        $this->middleware('can:admin.cattle-genealogy.destroy')->only('destroy');
    }

    public function index(): View
    {
        $cattle = Cattle::with(['breed', 'ranch', 'currentOwner'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('admin.cattle-genealogy.index', [
            'cattle' => $cattle,
            'relativeCattle' => $cattle,
            'breeds' => Breed::where('status', 'active')->orderBy('name')->get(),
            'cattleOptions' => $cattle->map(fn (Cattle $animal) => $this->cattleOption($animal))->values(),
        ]);
    }

    public function list(): JsonResponse
    {
        $links = CattleGenealogyLink::query()
            ->with(['cattle.breed', 'relativeCattle.breed', 'breed'])
            ->latest('id');

        return DataTables::eloquent($links)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (CattleGenealogyLink $link) => $this->cattleLabel($link->cattle))
            ->addColumn('cattle_code', fn (CattleGenealogyLink $link) => $link->cattle?->code ?: '—')
            ->editColumn('relation_type', fn (CattleGenealogyLink $link) => $this->relationBadge($link->relation_type))
            ->editColumn('generation_level', fn (CattleGenealogyLink $link) => $this->generationBadge((int) $link->generation_level))
            ->addColumn('relative_name', fn (CattleGenealogyLink $link) => $this->relativeDisplayName($link))
            ->addColumn('breed_name', fn (CattleGenealogyLink $link) => $link->breed?->name ?: $link->relativeCattle?->breed?->name ?: '—')
            ->editColumn('purity_percentage', fn (CattleGenealogyLink $link) => $link->purity_percentage !== null
                ? e(number_format((float) $link->purity_percentage, 2)).'%'
                : '—')
            ->editColumn('created_at', fn (CattleGenealogyLink $link) => $link->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (CattleGenealogyLink $link) => view(
                'admin.cattle-genealogy.partials.acciones',
                ['link' => $link]
            )->render())
            ->rawColumns(['cattle_name', 'cattle_code', 'relation_type', 'generation_level', 'relative_name', 'breed_name', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureRelativeCattleCanBeParent($data);
        $data = $this->syncRelativeData($data);
        $this->ensureUniqueGenealogyLink($data);
        $this->ensureCattleParentCanBeSynced($data);

        DB::transaction(function () use ($data): void {
            $link = CattleGenealogyLink::create($data);
            $this->syncCattleParent($link);
        });

        return response()->json([
            'message' => 'Registro genealógico guardado correctamente.',
        ]);
    }

    public function show(CattleGenealogyLink $cattleGenealogyLink): JsonResponse
    {
        $cattleGenealogyLink->load([
            'cattle.breed',
            'cattle.ranch',
            'cattle.currentOwner',
            'relativeCattle.breed',
            'breed',
        ]);

        return response()->json([
            'genealogy' => array_merge($cattleGenealogyLink->toArray(), [
                'relation_label' => $this->relationLabel($cattleGenealogyLink->relation_type),
                'generation_label' => $this->generationLabel((int) $cattleGenealogyLink->generation_level),
                'relative_display_name' => $this->relativeDisplayName($cattleGenealogyLink),
                'relative_display_code' => $this->relativeDisplayCode($cattleGenealogyLink),
                'relative_breed_name' => $cattleGenealogyLink->breed?->name ?: $cattleGenealogyLink->relativeCattle?->breed?->name,
                'cattle_label' => $this->cattleLabel($cattleGenealogyLink->cattle),
                'cattle_code' => $cattleGenealogyLink->cattle?->code,
                'cattle_name' => $cattleGenealogyLink->cattle?->name,
                'cattle_breed_name' => $cattleGenealogyLink->cattle?->breed?->name,
                'cattle_ranch_name' => $cattleGenealogyLink->cattle?->ranch?->name,
                'cattle_owner_name' => $this->ownerDisplayName($cattleGenealogyLink->cattle?->currentOwner),
                'cattle_photo_url' => $this->mainPhotoUrl($cattleGenealogyLink->cattle?->main_photo_path),
                'relative_registered_label' => $cattleGenealogyLink->relative_cattle_id
                    ? $this->cattleLabel($cattleGenealogyLink->relativeCattle)
                    : 'Familiar no registrado en el sistema',
                'created_at_formatted' => $cattleGenealogyLink->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $cattleGenealogyLink->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, CattleGenealogyLink $cattleGenealogyLink): JsonResponse
    {
        $data = $this->validatedData($request, $cattleGenealogyLink);
        $this->ensureRelativeCattleCanBeParent($data);
        $data = $this->syncRelativeData($data);
        $this->ensureUniqueGenealogyLink($data, $cattleGenealogyLink->id);

        $previous = [
            'cattle_id' => $cattleGenealogyLink->cattle_id,
            'relation_type' => $cattleGenealogyLink->relation_type,
            'relative_cattle_id' => $cattleGenealogyLink->relative_cattle_id,
        ];

        DB::transaction(function () use ($cattleGenealogyLink, $data, $previous): void {
            $this->clearPreviousCattleParentIfNeeded($previous, $data);

            $cattleGenealogyLink->update($data);
            $this->syncCattleParent($cattleGenealogyLink->refresh());
        });

        return response()->json([
            'message' => 'Registro genealógico actualizado correctamente.',
        ]);
    }

    public function destroy(CattleGenealogyLink $cattleGenealogyLink): JsonResponse
    {
        $cattleGenealogyLink->delete();

        return response()->json([
            'message' => 'Registro genealógico eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request, ?CattleGenealogyLink $link = null): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'relative_cattle_id' => [
                'nullable',
                'exists:cattle,id',
                'different:cattle_id',
            ],
            'relation_type' => ['required', Rule::in(self::RELATION_TYPES)],
            'generation_level' => ['required', 'integer', 'min:1', 'max:9'],
            'relative_code' => ['nullable', 'string', 'max:120'],
            'relative_name' => [Rule::requiredIf(! $request->filled('relative_cattle_id')), 'nullable', 'string', 'max:255'],
            'breed_id' => ['nullable', 'exists:breeds,id'],
            'purity_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el animal principal.',
            'cattle_id.exists' => 'El animal principal seleccionado no es válido.',
            'relative_cattle_id.exists' => 'El familiar seleccionado no es válido.',
            'relative_cattle_id.different' => 'El familiar no puede ser el mismo animal principal.',
            'relative_name.required' => 'Ingrese el nombre del familiar o seleccione un familiar registrado.',
            'relation_type.required' => 'Seleccione el tipo de relación.',
            'relation_type.in' => 'El tipo de relación seleccionado no es válido.',
            'generation_level.required' => 'Seleccione el nivel de generación.',
            'generation_level.integer' => 'El nivel de generación debe ser numérico.',
            'generation_level.min' => 'El nivel de generación debe ser al menos 1.',
            'breed_id.exists' => 'La raza seleccionada no es válida.',
            'purity_percentage.numeric' => 'La pureza debe ser numérica.',
            'purity_percentage.min' => 'La pureza no puede ser menor a 0.',
            'purity_percentage.max' => 'La pureza no puede superar 100.',
        ]);
    }

    private function syncRelativeData(array $data): array
    {
        if (! empty($data['relative_cattle_id'])) {
            $relative = Cattle::find($data['relative_cattle_id']);

            if ($relative) {
                $data['relative_code'] = $relative->code;
                $data['relative_name'] = $relative->name;
                $data['breed_id'] = $relative->breed_id;
                $data['purity_percentage'] = $relative->purity_percentage;
            }
        }

        return $data;
    }

    private function ensureRelativeCattleCanBeParent(array $data): void
    {
        if (empty($data['relative_cattle_id']) || ! in_array($data['relation_type'] ?? null, ['father', 'mother'], true)) {
            return;
        }

        $relative = Cattle::find($data['relative_cattle_id']);

        if (! $relative) {
            return;
        }

        if ($data['relation_type'] === 'father' && $relative->sex !== 'male') {
            throw ValidationException::withMessages([
                'relative_cattle_id' => 'El padre seleccionado debe ser un animal macho.',
            ]);
        }

        if ($data['relation_type'] === 'mother' && $relative->sex !== 'female') {
            throw ValidationException::withMessages([
                'relative_cattle_id' => 'La madre seleccionada debe ser un animal hembra.',
            ]);
        }
    }

    private function ensureUniqueGenealogyLink(array $data, ?int $ignoreId = null): void
    {
        if (in_array($data['relation_type'], ['father', 'mother'], true)) {
            $exists = CattleGenealogyLink::query()
                ->where('cattle_id', $data['cattle_id'])
                ->where('relation_type', $data['relation_type'])
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'relation_type' => $data['relation_type'] === 'father'
                        ? 'Este animal ya tiene un padre registrado en genealogia.'
                        : 'Este animal ya tiene una madre registrada en genealogia.',
                ]);
            }

            return;
        }

        $query = CattleGenealogyLink::query()
            ->where('cattle_id', $data['cattle_id'])
            ->where('relation_type', $data['relation_type'])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId));

        if (! empty($data['relative_cattle_id'])) {
            $query->where('relative_cattle_id', $data['relative_cattle_id']);
        } else {
            $query->whereNull('relative_cattle_id')
                ->whereRaw('LOWER(relative_name) = ?', [strtolower(trim((string) ($data['relative_name'] ?? '')))]);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'relative_cattle_id' => 'Ya existe esta relación genealógica para el animal principal.',
            ]);
        }
    }

    private function ensureCattleParentCanBeSynced(array $data): void
    {
        if (empty($data['relative_cattle_id']) || ! in_array($data['relation_type'], ['father', 'mother'], true)) {
            return;
        }

        $cattle = Cattle::find($data['cattle_id']);

        if (! $cattle) {
            return;
        }

        if (
            $data['relation_type'] === 'father'
            && $cattle->father_id
            && (int) $cattle->father_id !== (int) $data['relative_cattle_id']
        ) {
            throw ValidationException::withMessages([
                'relative_cattle_id' => 'Este animal ya tiene un padre registrado. Edite el registro existente si desea cambiarlo.',
            ]);
        }

        if (
            $data['relation_type'] === 'mother'
            && $cattle->mother_id
            && (int) $cattle->mother_id !== (int) $data['relative_cattle_id']
        ) {
            throw ValidationException::withMessages([
                'relative_cattle_id' => 'Este animal ya tiene una madre registrada. Edite el registro existente si desea cambiarlo.',
            ]);
        }
    }

    private function syncCattleParent(CattleGenealogyLink $link): void
    {
        if (! $link->relative_cattle_id || ! in_array($link->relation_type, ['father', 'mother'], true)) {
            return;
        }

        $cattle = Cattle::find($link->cattle_id);

        if (! $cattle) {
            return;
        }

        if ($link->relation_type === 'father') {
            $cattle->update(['father_id' => $link->relative_cattle_id]);
        }

        if ($link->relation_type === 'mother') {
            $cattle->update(['mother_id' => $link->relative_cattle_id]);
        }
    }

    private function clearPreviousCattleParentIfNeeded(array $previous, array $newData): void
    {
        if (
            empty($previous['relative_cattle_id'])
            || ! in_array($previous['relation_type'], ['father', 'mother'], true)
        ) {
            return;
        }

        $changed = (int) $previous['cattle_id'] !== (int) $newData['cattle_id']
            || $previous['relation_type'] !== $newData['relation_type']
            || (int) $previous['relative_cattle_id'] !== (int) ($newData['relative_cattle_id'] ?? 0);

        if (! $changed) {
            return;
        }

        $cattle = Cattle::find($previous['cattle_id']);

        if (! $cattle) {
            return;
        }

        if ($previous['relation_type'] === 'father' && (int) $cattle->father_id === (int) $previous['relative_cattle_id']) {
            $cattle->update(['father_id' => null]);
        }

        if ($previous['relation_type'] === 'mother' && (int) $cattle->mother_id === (int) $previous['relative_cattle_id']) {
            $cattle->update(['mother_id' => null]);
        }
    }

    private function cattleOption(Cattle $cattle): array
    {
        return [
            'id' => $cattle->id,
            'code' => $cattle->code,
            'name' => $cattle->name,
            'sex' => $cattle->sex,
            'breed_id' => $cattle->breed_id,
            'breed_name' => $cattle->breed?->name,
            'purity_percentage' => $cattle->purity_percentage,
        ];
    }

    private function cattleLabel(?Cattle $cattle): string
    {
        if (! $cattle) {
            return '—';
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function relativeDisplayName(CattleGenealogyLink $link): string
    {
        return $link->relativeCattle
            ? $this->cattleLabel($link->relativeCattle)
            : ($link->relative_name ?: 'Familiar no registrado');
    }

    private function relativeDisplayCode(CattleGenealogyLink $link): ?string
    {
        return $link->relativeCattle?->code ?: $link->relative_code;
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

    private function relationLabel(string $type): string
    {
        return match ($type) {
            'father' => 'Padre',
            'mother' => 'Madre',
            'paternal_grandfather' => 'Abuelo paterno',
            'paternal_grandmother' => 'Abuela paterna',
            'maternal_grandfather' => 'Abuelo materno',
            'maternal_grandmother' => 'Abuela materna',
            default => 'Relación',
        };
    }

    private function generationLabel(int $level): string
    {
        return match ($level) {
            1 => '1ra generación',
            2 => '2da generación',
            3 => '3ra generación',
            default => $level.'ta generación',
        };
    }

    private function relationBadge(string $type): string
    {
        $label = $this->relationLabel($type);
        $class = in_array($type, ['father', 'mother'], true) ? 'success' : 'info';

        return '<span class="badge badge-'.$class.'">'.$label.'</span>';
    }

    private function generationBadge(int $level): string
    {
        return '<span class="badge badge-light border">'.$this->generationLabel($level).'</span>';
    }
}
