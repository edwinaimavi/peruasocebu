<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\Owner;
use App\Models\Ranch;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CattleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.cattle.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.cattle.store')->only('store');
        $this->middleware('can:admin.cattle.update')->only('update');
        $this->middleware('can:admin.cattle.destroy')->only('destroy');
    }

    public function index(): View
    {
        $breeds = Breed::where('status', 'active')->orderBy('name')->get();

        return view('admin.cattle.index', [
            'breeds' => $breeds,
            'cattleBreedOptions' => $breeds->map(fn (Breed $breed) => [
                'id' => $breed->id,
                'code' => $breed->code,
                'name' => $breed->name,
            ])->values(),
            'ranches' => Ranch::where('status', 'active')->orderBy('name')->get(),
            'owners' => Owner::where('status', 'active')->orderBy('full_name')->get(),
            'fathers' => Cattle::with('breed')->where('status', 'active')->where('sex', 'male')->orderBy('name')->get(),
            'mothers' => Cattle::with('breed')->where('status', 'active')->where('sex', 'female')->orderBy('name')->get(),
        ]);
    }

    public function list(): JsonResponse
    {
        $cattle = Cattle::query()
            ->with(['breed', 'ranch', 'currentOwner'])
            ->latest('id');

        return DataTables::eloquent($cattle)
            ->addIndexColumn()
            ->addColumn('photo', fn (Cattle $cattle) => $this->tablePhoto($cattle))
            ->editColumn('code', fn (Cattle $cattle) => '<span class="badge badge-light border px-2 py-1">'.e($cattle->code).'</span>')
            ->addColumn('breed_name', fn (Cattle $cattle) => $cattle->breed?->name ?: '—')
            ->editColumn('sex', fn (Cattle $cattle) => $this->sexBadge($cattle->sex))
            ->addColumn('ranch_name', fn (Cattle $cattle) => $cattle->ranch?->name ?: '—')
            ->addColumn('owner_name', fn (Cattle $cattle) => $this->ownerDisplayName($cattle->currentOwner) ?: '—')
            ->editColumn('purity_percentage', fn (Cattle $cattle) => $cattle->purity_percentage !== null
                ? e(number_format((float) $cattle->purity_percentage, 2)).'%'
                : '—')
            ->editColumn('status', fn (Cattle $cattle) => $this->statusBadge($cattle->status))
            ->editColumn('sale_status', fn (Cattle $cattle) => $this->saleStatusBadge($cattle->sale_status))
            ->editColumn('is_public', fn (Cattle $cattle) => $cattle->is_public
                ? '<span class="badge badge-success">Público</span>'
                : '<span class="badge badge-secondary">Privado</span>')
            ->editColumn('created_at', fn (Cattle $cattle) => $cattle->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (Cattle $cattle) => view(
                'admin.cattle.partials.acciones',
                compact('cattle')
            )->render())
            ->rawColumns(['photo', 'code', 'name', 'breed_name', 'sex', 'ranch_name', 'owner_name', 'status', 'sale_status', 'is_public', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $data['code'] = $this->generateCattleCode((int) $data['breed_id']);
        $data['is_public'] = $request->boolean('is_public');
        $uploadedPath = $this->storeMainPhoto($request);

        try {
            DB::transaction(function () use ($data, $uploadedPath): void {
                $cattle = Cattle::create(array_merge($data, $uploadedPath));

                $this->syncGenealogyParent($cattle, 'father', $data['father_id'] ?? null);
                $this->syncGenealogyParent($cattle, 'mother', $data['mother_id'] ?? null);
            });
        } catch (\Throwable $exception) {
            $this->deleteMainPhoto($uploadedPath['main_photo_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Ganado registrado correctamente.',
        ]);
    }

    public function show(Cattle $cattle): JsonResponse
    {
        $cattle->load([
            'breed',
            'ranch',
            'currentOwner',
            'father.breed',
            'mother.breed',
            'genealogyLinks' => fn ($query) => $query
                ->with(['breed', 'relativeCattle.breed'])
                ->whereIn('relation_type', ['father', 'mother'])
                ->oldest('id'),
        ]);

        $manualFather = $this->manualParentLink($cattle, 'father');
        $manualMother = $this->manualParentLink($cattle, 'mother');
        $birthDate = $cattle->birth_date;
        $ageText = $this->calculateAgeText($birthDate);

        return response()->json([
            'cattle' => array_merge($cattle->toArray(), [
                'photo_url' => $this->mainPhotoUrl($cattle->main_photo_path),
                'breed_name' => $cattle->breed?->name,
                'breed_code' => $cattle->breed?->code,
                'ranch_name' => $cattle->ranch?->name,
                'owner_name' => $this->ownerDisplayName($cattle->currentOwner),
                'father_label' => $this->parentLabel($cattle->father) ?: $this->manualParentLabel($manualFather),
                'father_breed_name' => $cattle->father?->breed?->name ?: $this->manualParentBreedName($manualFather),
                'mother_label' => $this->parentLabel($cattle->mother) ?: $this->manualParentLabel($manualMother),
                'mother_breed_name' => $cattle->mother?->breed?->name ?: $this->manualParentBreedName($manualMother),
                'sex_label' => $this->sexLabel($cattle->sex),
                'status_label' => $this->statusLabel($cattle->status),
                'sale_status_label' => $this->saleStatusLabel($cattle->sale_status),
                'is_public_label' => $cattle->is_public ? 'Público' : 'Privado',
                'birth_date' => $birthDate?->format('Y-m-d'),
                'birth_date_formatted' => $birthDate ? $birthDate->format('d/m/Y') : 'No registrado',
                'age_label' => $ageText,
                'age_text' => $ageText,
                'created_at_formatted' => $cattle->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $cattle->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, Cattle $cattle): JsonResponse
    {
        $data = $this->validatedData($request, $cattle);
        $data['code'] = $cattle->code ?: $this->generateCattleCode((int) $data['breed_id'], $cattle->id);
        $data['is_public'] = $request->boolean('is_public');
        $uploadedPath = $this->storeMainPhoto($request);
        $oldPhotoPath = $uploadedPath ? $cattle->main_photo_path : null;

        try {
            DB::transaction(function () use ($cattle, $data, $uploadedPath): void {
                $cattle->update(array_merge($data, $uploadedPath));
                $cattle->refresh();

                $this->syncGenealogyParent($cattle, 'father', $data['father_id'] ?? null);
                $this->syncGenealogyParent($cattle, 'mother', $data['mother_id'] ?? null);
            });
        } catch (\Throwable $exception) {
            $this->deleteMainPhoto($uploadedPath['main_photo_path'] ?? null);
            throw $exception;
        }

        $this->deleteMainPhoto($oldPhotoPath);

        return response()->json([
            'message' => 'Ganado actualizado correctamente.',
        ]);
    }

    public function destroy(Cattle $cattle): JsonResponse
    {
        $cattle->delete();

        return response()->json([
            'message' => 'Ganado eliminado correctamente.',
        ]);
    }

    private function validatedData(Request $request, ?Cattle $cattle = null): array
    {
        return $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:50',
            ],
            'name' => ['required', 'string', 'max:255'],
            'breed_id' => ['required', 'exists:breeds,id'],
            'ranch_id' => ['required', 'exists:ranches,id'],
            'current_owner_id' => ['nullable', 'exists:owners,id'],
            'father_id' => [
                'nullable',
                Rule::exists('cattle', 'id')->where('sex', 'male'),
                Rule::notIn(array_filter([$cattle?->id])),
            ],
            'mother_id' => [
                'nullable',
                Rule::exists('cattle', 'id')->where('sex', 'female'),
                Rule::notIn(array_filter([$cattle?->id])),
            ],
            'sex' => ['required', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'color' => ['nullable', 'string', 'max:120'],
            'weight_kg' => ['nullable', 'numeric', 'min:0'],
            'height_cm' => ['nullable', 'numeric', 'min:0'],
            'ear_tag' => ['nullable', 'string', 'max:120'],
            'chip_number' => ['nullable', 'string', 'max:120'],
            'purity_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:active,dead,discarded'],
            'sale_status' => ['required', 'in:available,reserved,sold,not_available'],
            'main_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_public' => ['nullable', 'boolean'],
            'observations' => ['nullable', 'string'],
        ], [
            'name.required' => 'El nombre del animal es obligatorio.',
            'breed_id.required' => 'Seleccione la raza.',
            'breed_id.exists' => 'La raza seleccionada no es válida.',
            'ranch_id.required' => 'Seleccione el criadero o hacienda.',
            'ranch_id.exists' => 'El criadero seleccionado no es válido.',
            'current_owner_id.exists' => 'El propietario seleccionado no es válido.',
            'father_id.exists' => 'El padre seleccionado debe ser un animal macho.',
            'father_id.not_in' => 'El padre no puede ser el mismo animal.',
            'mother_id.exists' => 'La madre seleccionada debe ser un animal hembra.',
            'mother_id.not_in' => 'La madre no puede ser el mismo animal.',
            'sex.required' => 'Seleccione el sexo.',
            'sex.in' => 'El sexo seleccionado no es válido.',
            'birth_date.date' => 'Ingrese una fecha de nacimiento válida.',
            'weight_kg.numeric' => 'El peso debe ser numérico.',
            'weight_kg.min' => 'El peso no puede ser negativo.',
            'height_cm.numeric' => 'La altura debe ser numérica.',
            'height_cm.min' => 'La altura no puede ser negativa.',
            'purity_percentage.numeric' => 'La pureza debe ser numérica.',
            'purity_percentage.min' => 'La pureza no puede ser menor a 0.',
            'purity_percentage.max' => 'La pureza no puede superar 100.',
            'status.required' => 'Seleccione el estado del ganado.',
            'status.in' => 'El estado seleccionado no es válido.',
            'sale_status.required' => 'Seleccione el estado de venta.',
            'sale_status.in' => 'El estado de venta seleccionado no es válido.',
            'main_photo.image' => 'La foto principal debe ser una imagen.',
            'main_photo.mimes' => 'La foto principal debe ser JPG, PNG o WEBP.',
            'main_photo.max' => 'La foto principal no debe superar los 4 MB.',
        ]);
    }

    private function generateCattleCode(int $breedId, ?int $ignoreId = null): string
    {
        $prefix = $this->cattleCodePrefix($breedId);

        for ($number = 1; $number <= 999999; $number++) {
            $code = $prefix.'-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);

            $exists = Cattle::query()
                ->where('code', $code)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists();

            if (! $exists) {
                return $code;
            }
        }

        throw ValidationException::withMessages([
            'code' => 'No se pudo generar un código disponible para el ganado. Intente con otra raza.',
        ]);
    }

    private function cattleCodePrefix(int $breedId): string
    {
        $breed = Breed::find($breedId);
        $base = $breed?->code ?: $breed?->name ?: 'GAN';
        $prefix = preg_replace('/[^A-Z0-9]/', '', strtoupper(Str::ascii($base))) ?? '';

        return $prefix ?: 'GAN';
    }

    private function storeMainPhoto(Request $request): array
    {
        if (! $request->hasFile('main_photo')) {
            return [];
        }

        return [
            'main_photo_path' => $request->file('main_photo')->store('cattle/photos', 'public'),
        ];
    }

    private function deleteMainPhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function mainPhotoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }

    private function tablePhoto(Cattle $cattle): string
    {
        $url = $this->mainPhotoUrl($cattle->main_photo_path);

        if (! $url) {
            return '<span class="cattle-table-photo cattle-table-photo-placeholder"><i class="fas fa-paw"></i></span>';
        }

        return '<img class="cattle-table-photo" src="'.e($url).'" alt="Foto de '.e($cattle->name).'">';
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

    private function parentLabel(?Cattle $cattle): ?string
    {
        if (! $cattle) {
            return null;
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function syncGenealogyParent(Cattle $cattle, string $relationType, ?int $relativeCattleId): void
    {
        $link = CattleGenealogyLink::query()
            ->where('cattle_id', $cattle->id)
            ->where('relation_type', $relationType)
            ->oldest('id')
            ->first();

        if (! $relativeCattleId) {
            if (! $link) {
                return;
            }

            if ($link->relative_cattle_id && blank($link->notes)) {
                $link->delete();

                return;
            }

            if ($link->relative_cattle_id) {
                $link->update(['relative_cattle_id' => null]);
            }

            return;
        }

        $relative = Cattle::findOrFail($relativeCattleId);

        $data = [
            'relative_cattle_id' => $relative->id,
            'generation_level' => 1,
            'relative_code' => $relative->code,
            'relative_name' => $relative->name,
            'breed_id' => $relative->breed_id,
            'purity_percentage' => $relative->purity_percentage,
        ];

        if ($link) {
            $link->update($data);

            return;
        }

        CattleGenealogyLink::create(array_merge($data, [
            'cattle_id' => $cattle->id,
            'relation_type' => $relationType,
        ]));
    }

    private function manualParentLink(Cattle $cattle, string $relationType): ?CattleGenealogyLink
    {
        return $cattle->genealogyLinks
            ->first(fn (CattleGenealogyLink $link) => $link->relation_type === $relationType && ! $link->relative_cattle_id);
    }

    private function manualParentLabel(?CattleGenealogyLink $link): ?string
    {
        if (! $link || ! $link->relative_name) {
            return null;
        }

        return trim(($link->relative_code ? $link->relative_code.' - ' : '').$link->relative_name);
    }

    private function manualParentBreedName(?CattleGenealogyLink $link): ?string
    {
        return $link?->breed?->name;
    }

    private function calculateAgeText(null|string|CarbonInterface $birthDate): string
    {
        if (! $birthDate) {
            return 'No registrado';
        }

        $birthDate = $birthDate instanceof CarbonInterface
            ? $birthDate
            : Carbon::parse($birthDate);

        $now = now();

        if ($birthDate->greaterThan($now)) {
            return "Fecha inv\u{00E1}lida";
        }

        $years = (int) $birthDate->diffInYears($now);

        if ($years >= 1) {
            return $years.' '.($years === 1 ? "a\u{00F1}o" : "a\u{00F1}os");
        }

        $months = (int) $birthDate->diffInMonths($now);

        if ($months >= 1) {
            return $months.' '.($months === 1 ? 'mes' : 'meses');
        }

        $days = (int) $birthDate->diffInDays($now);

        return $days.' '.($days === 1 ? "d\u{00ED}a" : "d\u{00ED}as");
    }

    private function sexLabel(?string $sex): string
    {
        return match ($sex) {
            'male' => 'Macho',
            'female' => 'Hembra',
            default => '—',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'active' => 'Activo',
            'dead' => 'Fallecido',
            'discarded' => 'Descartado',
            default => '—',
        };
    }

    private function saleStatusLabel(?string $saleStatus): string
    {
        return match ($saleStatus) {
            'available' => 'Disponible',
            'reserved' => 'Reservado',
            'sold' => 'Vendido',
            'not_available' => 'No disponible',
            default => '—',
        };
    }

    private function sexBadge(?string $sex): string
    {
        return match ($sex) {
            'male' => '<span class="badge badge-primary">Macho</span>',
            'female' => '<span class="badge badge-info">Hembra</span>',
            default => '<span class="text-muted">—</span>',
        };
    }

    private function statusBadge(?string $status): string
    {
        return match ($status) {
            'active' => '<span class="badge badge-success">Activo</span>',
            'dead' => '<span class="badge badge-danger">Fallecido</span>',
            'discarded' => '<span class="badge badge-warning">Descartado</span>',
            default => '<span class="text-muted">—</span>',
        };
    }

    private function saleStatusBadge(?string $saleStatus): string
    {
        return match ($saleStatus) {
            'available' => '<span class="badge badge-success">Disponible</span>',
            'reserved' => '<span class="badge badge-warning">Reservado</span>',
            'sold' => '<span class="badge badge-info">Vendido</span>',
            'not_available' => '<span class="badge badge-secondary">No disponible</span>',
            default => '<span class="text-muted">—</span>',
        };
    }
}
