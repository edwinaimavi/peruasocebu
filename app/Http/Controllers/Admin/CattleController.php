<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Cattle;
use App\Models\CattleGenealogyLink;
use App\Models\CattlePhoto;
use App\Models\CattleSale;
use App\Models\Certificate;
use App\Models\Owner;
use App\Models\OwnershipHistory;
use App\Models\Ranch;
use App\Models\ReproductionRecord;
use App\Models\Treatment;
use App\Models\Vaccination;
use App\Models\VeterinaryRecord;
use App\Models\WeightRecord;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
        $this->ensureCattleGenealogyDataIsValid($data);
        $data['is_public'] = $request->boolean('is_public');
        $uploadedPath = $this->storeMainPhoto($request);
        $galleryPaths = $this->storeGalleryPhotos($request);

        try {
            $this->storeWithGeneratedCode($data, $uploadedPath, $galleryPaths);
        } catch (\Throwable $exception) {
            $this->deleteMainPhoto($uploadedPath['main_photo_path'] ?? null);
            $this->deletePhotoFiles($galleryPaths);
            throw $exception;
        }

        return response()->json([
            'message' => 'Ganado registrado correctamente.',
        ]);
    }

    private function storeWithGeneratedCode(array $data, array $uploadedPath, array $galleryPaths): void
    {
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            try {
                $data['code'] = $this->generateCattleCode((int) $data['breed_id']);

                DB::transaction(function () use ($data, $uploadedPath, $galleryPaths): void {
                    $cattle = Cattle::create(array_merge($data, $uploadedPath));
                    $this->createPhotoRecords($cattle, $uploadedPath['main_photo_path'] ?? null, $galleryPaths);
                    $this->syncOwnershipHistoryFromCattle($cattle, $data['current_owner_id'] ?? null);

                    $this->syncGenealogyParent($cattle, 'father', $data['father_id'] ?? null);
                    $this->syncGenealogyParent($cattle, 'mother', $data['mother_id'] ?? null);
                });

                return;
            } catch (QueryException $exception) {
                if (! $this->isDuplicateCattleCodeException($exception)) {
                    throw $exception;
                }
            }
        }

        throw ValidationException::withMessages([
            'code' => 'No se pudo generar un código único. Intente guardar nuevamente.',
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
            'photos',
            'ownershipHistories.owner',
            'sales.seller',
            'sales.buyer',
            'vaccinations.veterinarian',
            'treatments.veterinarian',
            'veterinaryRecords.veterinarian',
            'weightRecords',
            'reproductionRecords.partner',
            'reproductionRecords.offspring',
            'reproductionAsPartner.cattle',
            'reproductionAsPartner.offspring',
            'certificates',
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
                'photos' => $cattle->photos->map(fn (CattlePhoto $photo) => [
                    'id' => $photo->id,
                    'image_path' => $photo->image_path,
                    'image_url' => $this->mainPhotoUrl($photo->image_path),
                    'title' => $photo->title,
                    'description' => $photo->description,
                    'is_main' => $photo->is_main,
                    'sort_order' => $photo->sort_order,
                ])->values(),
                'breed_name' => $cattle->breed?->name,
                'breed_code' => $cattle->breed?->code,
                'ranch_name' => $cattle->ranch?->name,
                'owner_name' => $this->ownerDisplayName($cattle->currentOwner),
                'ownership_histories' => $cattle->ownershipHistories->map(fn (OwnershipHistory $history) => [
                    'id' => $history->id,
                    'owner_name' => $this->ownerDisplayName($history->owner),
                    'start_date' => $history->start_date?->format('d/m/Y'),
                    'end_date' => $history->end_date?->format('d/m/Y') ?: 'Actual',
                    'acquisition_type_label' => $this->ownershipAcquisitionLabel($history->acquisition_type),
                    'is_current' => $history->is_current,
                ])->values(),
                'sales' => $cattle->sales->sortByDesc('sale_date')->map(fn (CattleSale $sale) => [
                    'id' => $sale->id,
                    'seller_name' => $this->ownerDisplayName($sale->seller),
                    'buyer_name' => $this->ownerDisplayName($sale->buyer),
                    'sale_date' => $sale->sale_date?->format('d/m/Y'),
                    'sale_price' => trim(($sale->currency ?: 'PEN').' '.number_format((float) $sale->sale_price, 2)),
                    'payment_method_label' => $this->salePaymentLabel($sale->payment_method),
                    'status' => $sale->status,
                    'status_label' => $this->cattleSaleOperationStatusLabel($sale->status),
                ])->values(),
                'certificates' => $cattle->certificates->take(5)->map(fn (Certificate $certificate) => [
                    'id' => $certificate->id,
                    'certificate_number' => $certificate->certificate_number,
                    'certificate_type' => $certificate->certificate_type,
                    'certificate_type_label' => $this->certificateTypeLabel($certificate->certificate_type),
                    'issue_date' => $certificate->issue_date?->format('d/m/Y'),
                    'status' => $certificate->status,
                    'status_label' => $this->certificateStatusLabel($certificate->status),
                    'pdf_url' => $this->mainPhotoUrl($certificate->pdf_path),
                    'verify_url' => route('certificates.verify', $certificate->verification_code),
                ])->values(),
                'veterinary_records' => $cattle->veterinaryRecords->take(5)->map(fn (VeterinaryRecord $record) => [
                    'id' => $record->id,
                    'record_date' => $record->record_date?->format('d/m/Y'),
                    'record_type' => $record->record_type,
                    'record_type_label' => $this->veterinaryRecordTypeLabel($record->record_type),
                    'veterinarian_name' => $record->veterinarian?->full_name,
                    'diagnosis' => $record->diagnosis,
                    'next_visit_date' => $record->next_visit_date?->format('d/m/Y'),
                ])->values(),
                'vaccinations' => $cattle->vaccinations->take(5)->map(fn (Vaccination $vaccination) => [
                    'id' => $vaccination->id,
                    'vaccine_name' => $vaccination->vaccine_name,
                    'dose' => $vaccination->dose,
                    'batch_number' => $vaccination->batch_number,
                    'application_date' => $vaccination->application_date?->format('d/m/Y'),
                    'next_due_date' => $vaccination->next_due_date?->format('d/m/Y'),
                    'next_due_status' => $this->vaccinationNextDueStatus($vaccination),
                    'next_due_status_label' => $this->vaccinationNextDueStatusLabel($vaccination),
                    'veterinarian_name' => $vaccination->veterinarian?->full_name,
                ])->values(),
                'treatments' => $cattle->treatments->take(5)->map(fn (Treatment $treatment) => [
                    'id' => $treatment->id,
                    'treatment_date' => $treatment->treatment_date?->format('d/m/Y'),
                    'treatment_name' => $treatment->treatment_name,
                    'medicine' => $treatment->medicine,
                    'dose' => $treatment->dose,
                    'duration' => $treatment->duration,
                    'veterinarian_name' => $treatment->veterinarian?->full_name,
                ])->values(),
                'weight_records' => $cattle->weightRecords->take(5)->map(fn (WeightRecord $record) => [
                    'id' => $record->id,
                    'record_date' => $record->record_date?->format('d/m/Y'),
                    'weight_kg' => $record->weight_kg !== null ? number_format((float) $record->weight_kg, 2).' kg' : '-',
                    'body_condition' => $record->body_condition ?: 'Sin dato',
                    'observations' => $record->observations,
                ])->values(),
                'latest_weight_record' => $cattle->weightRecords->first()
                    ? [
                        'weight_kg' => number_format((float) $cattle->weightRecords->first()->weight_kg, 2).' kg',
                        'record_date' => $cattle->weightRecords->first()->record_date?->format('d/m/Y'),
                    ]
                    : null,
                'previous_weight_record' => $cattle->weightRecords->skip(1)->first()
                    ? [
                        'weight_kg' => number_format((float) $cattle->weightRecords->skip(1)->first()->weight_kg, 2).' kg',
                        'difference' => number_format(
                            (float) $cattle->weightRecords->first()->weight_kg - (float) $cattle->weightRecords->skip(1)->first()->weight_kg,
                            2
                        ),
                    ]
                    : null,
                'reproduction_records' => $cattle->reproductionRecords->take(5)->map(fn (ReproductionRecord $record) => [
                    'id' => $record->id,
                    'reproduction_date' => $record->reproduction_date?->format('d/m/Y'),
                    'method' => $record->method,
                    'method_label' => $this->reproductionMethodLabel($record->method),
                    'partner_label' => $this->parentLabel($record->partner) ?: 'Sin pareja registrada',
                    'pregnancy_result' => $record->pregnancy_result,
                    'pregnancy_result_label' => $this->pregnancyResultLabel($record->pregnancy_result),
                    'birth_date' => $record->birth_date?->format('d/m/Y') ?: 'Sin parto',
                    'offspring_label' => $this->parentLabel($record->offspring) ?: 'Sin cria vinculada',
                ])->values(),
                'reproduction_as_partner' => $cattle->reproductionAsPartner->take(5)->map(fn (ReproductionRecord $record) => [
                    'id' => $record->id,
                    'reproduction_date' => $record->reproduction_date?->format('d/m/Y'),
                    'method_label' => $this->reproductionMethodLabel($record->method),
                    'cattle_label' => $this->parentLabel($record->cattle) ?: '-',
                    'pregnancy_result' => $record->pregnancy_result,
                    'pregnancy_result_label' => $this->pregnancyResultLabel($record->pregnancy_result),
                    'offspring_label' => $this->parentLabel($record->offspring) ?: 'Sin cria vinculada',
                ])->values(),
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
        $this->ensureCattleGenealogyDataIsValid($data, $cattle);
        $data['code'] = $cattle->code ?: $this->generateCattleCode((int) $data['breed_id'], $cattle->id);
        $data['is_public'] = $request->boolean('is_public');
        $uploadedPath = $this->storeMainPhoto($request);
        $galleryPaths = $this->storeGalleryPhotos($request);

        try {
            DB::transaction(function () use ($cattle, $data, $uploadedPath, $galleryPaths): void {
                $oldOwnerId = $cattle->current_owner_id;
                $cattle->update(array_merge($data, $uploadedPath));
                $cattle->refresh();
                $this->createPhotoRecords($cattle, $uploadedPath['main_photo_path'] ?? null, $galleryPaths);
                $this->syncOwnershipHistoryFromCattle($cattle, $data['current_owner_id'] ?? null, $oldOwnerId);

                $this->syncGenealogyParent($cattle, 'father', $data['father_id'] ?? null);
                $this->syncGenealogyParent($cattle, 'mother', $data['mother_id'] ?? null);
            });
        } catch (\Throwable $exception) {
            $this->deleteMainPhoto($uploadedPath['main_photo_path'] ?? null);
            $this->deletePhotoFiles($galleryPaths);
            throw $exception;
        }

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
                'exists:cattle,id',
                Rule::notIn(array_filter([$cattle?->id])),
            ],
            'mother_id' => [
                'nullable',
                'exists:cattle,id',
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
            'gallery_photos' => ['nullable', 'array'],
            'gallery_photos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
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
            'gallery_photos.*.image' => 'Cada foto de galería debe ser una imagen.',
            'gallery_photos.*.mimes' => 'Las fotos de galería deben ser JPG, PNG o WEBP.',
            'gallery_photos.*.max' => 'Cada foto de galería no debe superar los 4 MB.',
        ]);
    }

    private function ensureCattleGenealogyDataIsValid(array $data, ?Cattle $cattle = null): void
    {
        $this->ensureParentsAreDifferent($data);
        $this->ensureParentSexesAreValid($data);
        $this->ensureParentsDoNotCreateCircularGenealogy($data, $cattle);
        $this->ensureParentsAreOlderThanChild($data);

        if ($cattle) {
            $this->ensureBirthDateIsValidForExistingChildren($data, $cattle);
            $this->ensureSexCanBeChanged($data, $cattle);
        }
    }

    private function ensureParentsAreDifferent(array $data): void
    {
        if (
            ! empty($data['father_id'])
            && ! empty($data['mother_id'])
            && (int) $data['father_id'] === (int) $data['mother_id']
        ) {
            throw ValidationException::withMessages([
                'mother_id' => 'El padre y la madre no pueden ser el mismo animal.',
            ]);
        }
    }

    private function ensureParentSexesAreValid(array $data): void
    {
        if (! empty($data['father_id']) && Cattle::find($data['father_id'])?->sex !== 'male') {
            throw ValidationException::withMessages([
                'father_id' => 'El padre seleccionado debe ser un animal macho.',
            ]);
        }

        if (! empty($data['mother_id']) && Cattle::find($data['mother_id'])?->sex !== 'female') {
            throw ValidationException::withMessages([
                'mother_id' => 'La madre seleccionada debe ser un animal hembra.',
            ]);
        }
    }

    private function ensureParentsAreOlderThanChild(array $data): void
    {
        if (empty($data['birth_date'])) {
            return;
        }

        $this->ensureParentIsOlderThanChild($data['father_id'] ?? null, $data['birth_date'], 'father_id', 'padre');
        $this->ensureParentIsOlderThanChild($data['mother_id'] ?? null, $data['birth_date'], 'mother_id', 'madre');
    }

    private function ensureParentIsOlderThanChild(?int $parentId, string $childBirthDate, string $field, string $parentLabel): void
    {
        if (! $parentId) {
            return;
        }

        $parent = Cattle::find($parentId);

        if (! $parent?->birth_date) {
            return;
        }

        $childBirthDate = Carbon::parse($childBirthDate);

        if ($parent->birth_date->lessThan($childBirthDate)) {
            return;
        }

        throw ValidationException::withMessages([
            $field => "La fecha de nacimiento del {$parentLabel} debe ser anterior a la fecha de nacimiento del hijo.",
        ]);
    }

    private function ensureParentsDoNotCreateCircularGenealogy(array $data, ?Cattle $cattle = null): void
    {
        if (! $cattle) {
            return;
        }

        foreach (['father_id', 'mother_id'] as $field) {
            if (
                ! empty($data[$field])
                && $this->isDescendantOf((int) $data[$field], (int) $cattle->id)
            ) {
                throw ValidationException::withMessages([
                    $field => 'No se puede asignar este familiar porque generaría una genealogía circular.',
                ]);
            }
        }
    }

    private function ensureBirthDateIsValidForExistingChildren(array $data, Cattle $cattle): void
    {
        if (empty($data['birth_date'])) {
            return;
        }

        $birthDate = Carbon::parse($data['birth_date']);
        $hasChildBornBeforeOrSameDay = $this->childBirthDates($cattle->id)
            ->contains(fn (CarbonInterface $childBirthDate) => ! $birthDate->lessThan($childBirthDate));

        if ($hasChildBornBeforeOrSameDay) {
            throw ValidationException::withMessages([
                'birth_date' => 'La fecha de nacimiento no es válida porque este animal tiene hijos registrados con fechas anteriores.',
            ]);
        }
    }

    private function childBirthDates(int $cattleId)
    {
        $directChildren = Cattle::query()
            ->where(fn ($query) => $query
                ->where('father_id', $cattleId)
                ->orWhere('mother_id', $cattleId))
            ->whereNotNull('birth_date')
            ->pluck('birth_date');

        $genealogyChildren = CattleGenealogyLink::query()
            ->with('cattle')
            ->where('relative_cattle_id', $cattleId)
            ->whereIn('relation_type', ['father', 'mother'])
            ->get()
            ->pluck('cattle.birth_date')
            ->filter();

        return $directChildren
            ->merge($genealogyChildren)
            ->filter()
            ->map(fn ($date) => $date instanceof CarbonInterface ? $date : Carbon::parse($date));
    }

    private function ensureSexCanBeChanged(array $data, Cattle $cattle): void
    {
        if (($data['sex'] ?? null) === $cattle->sex) {
            return;
        }

        if (($data['sex'] ?? null) === 'female' && $this->isUsedAsMaleRelative($cattle->id)) {
            throw ValidationException::withMessages([
                'sex' => 'No puedes cambiar este animal a hembra porque ya está registrado como padre.',
            ]);
        }

        if (($data['sex'] ?? null) === 'male' && $this->isUsedAsFemaleRelative($cattle->id)) {
            throw ValidationException::withMessages([
                'sex' => 'No puedes cambiar este animal a macho porque ya está registrado como madre.',
            ]);
        }
    }

    private function isUsedAsMaleRelative(int $cattleId): bool
    {
        return Cattle::query()->where('father_id', $cattleId)->exists()
            || CattleGenealogyLink::query()
                ->where('relative_cattle_id', $cattleId)
                ->whereIn('relation_type', ['father', 'paternal_grandfather', 'maternal_grandfather'])
                ->exists();
    }

    private function isUsedAsFemaleRelative(int $cattleId): bool
    {
        return Cattle::query()->where('mother_id', $cattleId)->exists()
            || CattleGenealogyLink::query()
                ->where('relative_cattle_id', $cattleId)
                ->whereIn('relation_type', ['mother', 'paternal_grandmother', 'maternal_grandmother'])
                ->exists();
    }

    private function isDescendantOf(int $possibleDescendantId, int $possibleAncestorId, array $visited = []): bool
    {
        if ($possibleDescendantId === $possibleAncestorId) {
            return true;
        }

        if (in_array($possibleDescendantId, $visited, true)) {
            return false;
        }

        $visited[] = $possibleDescendantId;

        $animal = Cattle::find($possibleDescendantId);
        $ancestorIds = collect([$animal?->father_id, $animal?->mother_id])
            ->merge(
                CattleGenealogyLink::query()
                    ->where('cattle_id', $possibleDescendantId)
                    ->whereIn('relation_type', ['father', 'mother'])
                    ->pluck('relative_cattle_id')
            )
            ->filter()
            ->unique()
            ->values();

        foreach ($ancestorIds as $ancestorId) {
            if ($this->isDescendantOf((int) $ancestorId, $possibleAncestorId, $visited)) {
                return true;
            }
        }

        return false;
    }

    private function generateCattleCode(int $breedId, ?int $ignoreId = null): string
    {
        $prefix = $this->cattleCodePrefix($breedId);
        $nextNumber = $this->nextCattleCodeNumber($prefix, $ignoreId);

        for ($number = $nextNumber; $number <= 999999; $number++) {
            $code = $prefix.'-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT);

            $exists = Cattle::withTrashed()
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

    private function nextCattleCodeNumber(string $prefix, ?int $ignoreId = null): int
    {
        $lastNumber = Cattle::withTrashed()
            ->where('code', 'like', $prefix.'-%')
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->pluck('code')
            ->map(function (?string $code): int {
                if (! $code || ! str_contains($code, '-')) {
                    return 0;
                }

                return (int) substr($code, strrpos($code, '-') + 1);
            })
            ->max();

        return ((int) $lastNumber) + 1;
    }

    private function isDuplicateCattleCodeException(QueryException $exception): bool
    {
        $errorCode = (string) ($exception->errorInfo[1] ?? '');

        return $errorCode === '1062'
            && str_contains($exception->getMessage(), 'cattle_code_unique');
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

    private function storeGalleryPhotos(Request $request): array
    {
        if (! $request->hasFile('gallery_photos')) {
            return [];
        }

        return collect($request->file('gallery_photos'))
            ->filter()
            ->map(fn ($file) => $file->store('cattle/photos', 'public'))
            ->values()
            ->all();
    }

    private function createPhotoRecords(Cattle $cattle, ?string $mainPhotoPath, array $galleryPaths): void
    {
        if ($mainPhotoPath) {
            $mainPhoto = CattlePhoto::create([
                'cattle_id' => $cattle->id,
                'image_path' => $mainPhotoPath,
                'title' => 'Foto principal',
                'is_main' => true,
                'sort_order' => 0,
            ]);

            $this->setMainPhotoRecord($mainPhoto);
        }

        foreach ($galleryPaths as $index => $path) {
            $isMain = ! $cattle->photos()->exists() && ! $cattle->main_photo_path;

            $photo = CattlePhoto::create([
                'cattle_id' => $cattle->id,
                'image_path' => $path,
                'title' => $isMain ? 'Foto principal' : null,
                'is_main' => $isMain,
                'sort_order' => $this->nextPhotoSortOrder($cattle) + $index,
            ]);

            if ($isMain) {
                $this->setMainPhotoRecord($photo);
            }
        }
    }

    private function setMainPhotoRecord(CattlePhoto $photo): void
    {
        CattlePhoto::where('cattle_id', $photo->cattle_id)
            ->whereKeyNot($photo->id)
            ->update(['is_main' => false]);

        $photo->update(['is_main' => true]);
        $photo->cattle->update(['main_photo_path' => $photo->image_path]);
    }

    private function nextPhotoSortOrder(Cattle $cattle): int
    {
        return ((int) $cattle->photos()->max('sort_order')) + 1;
    }

    private function deleteMainPhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function deletePhotoFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteMainPhoto($path);
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

    private function ownershipAcquisitionLabel(?string $type): string
    {
        return match ($type) {
            'birth' => 'Nacimiento',
            'purchase' => 'Compra',
            'sale' => 'Venta',
            'transfer' => 'Transferencia',
            'donation' => 'Donacion',
            'other' => 'Otro',
            default => '-',
        };
    }

    private function salePaymentLabel(?string $method): string
    {
        return match ($method) {
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'deposit' => 'Deposito',
            'other' => 'Otro',
            default => '-',
        };
    }

    private function certificateTypeLabel(?string $type): string
    {
        return match ($type) {
            'breed' => 'Raza',
            'genealogy' => 'Genealogia',
            'ownership' => 'Propiedad',
            'purity' => 'Pureza',
            default => '-',
        };
    }

    private function certificateStatusLabel(?string $status): string
    {
        return match ($status) {
            'issued' => 'Emitido',
            'cancelled' => 'Anulado',
            'expired' => 'Vencido',
            default => '-',
        };
    }

    private function cattleSaleOperationStatusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'cancelled' => 'Anulado',
            default => '-',
        };
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

    private function syncOwnershipHistoryFromCattle(Cattle $cattle, ?int $ownerId, ?int $oldOwnerId = null): void
    {
        if (! $ownerId) {
            if ($oldOwnerId) {
                OwnershipHistory::query()
                    ->where('cattle_id', $cattle->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            return;
        }

        if ($oldOwnerId && (int) $oldOwnerId === (int) $ownerId) {
            return;
        }

        $startDate = $oldOwnerId
            ? now()->toDateString()
            : ($cattle->birth_date ?: now()->toDateString());

        $existing = OwnershipHistory::query()
            ->where('cattle_id', $cattle->id)
            ->where('owner_id', $ownerId)
            ->whereDate('start_date', $startDate)
            ->first();

        if ($existing) {
            OwnershipHistory::query()
                ->where('cattle_id', $cattle->id)
                ->whereKeyNot($existing->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);

            $existing->update([
                'is_current' => true,
                'end_date' => null,
            ]);

            return;
        }

        $previousCurrent = OwnershipHistory::query()
            ->where('cattle_id', $cattle->id)
            ->where('is_current', true)
            ->get();

        foreach ($previousCurrent as $previous) {
            $newStartDate = Carbon::parse($startDate);
            $endDate = $newStartDate->copy()->subDay();

            if ($previous->start_date && $endDate->lt(Carbon::parse($previous->start_date))) {
                $endDate = $newStartDate;
            }

            $previous->update([
                'is_current' => false,
                'end_date' => $previous->end_date ?: $endDate->toDateString(),
            ]);
        }

        OwnershipHistory::create([
            'cattle_id' => $cattle->id,
            'owner_id' => $ownerId,
            'start_date' => $startDate,
            'end_date' => null,
            'acquisition_type' => $oldOwnerId ? 'other' : ($cattle->birth_date ? 'birth' : 'other'),
            'currency' => 'PEN',
            'is_current' => true,
            'notes' => 'Registro generado desde el modulo Ganado.',
        ]);
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

    private function veterinaryRecordTypeLabel(?string $type): string
    {
        return match ($type) {
            'checkup' => 'Revision',
            'illness' => 'Enfermedad',
            'control' => 'Control',
            'certification' => 'Certificacion',
            'emergency' => 'Emergencia',
            'other' => 'Otro',
            default => '-',
        };
    }

    private function vaccinationNextDueStatus(Vaccination $vaccination): string
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

    private function vaccinationNextDueStatusLabel(Vaccination $vaccination): string
    {
        return match ($this->vaccinationNextDueStatus($vaccination)) {
            'scheduled' => 'Programada',
            'today' => 'Aplicar hoy',
            'overdue' => 'Vencida',
            default => 'Sin proxima dosis',
        };
    }

    private function reproductionMethodLabel(?string $method): string
    {
        return match ($method) {
            'natural_mating' => 'Monta natural',
            'artificial_insemination' => 'Inseminacion artificial',
            'embryo_transfer' => 'Transferencia embrionaria',
            default => '-',
        };
    }

    private function pregnancyResultLabel(?string $result): string
    {
        return match ($result) {
            'positive' => 'Positivo',
            'negative' => 'Negativo',
            'pending' => 'Pendiente',
            default => '-',
        };
    }
}
