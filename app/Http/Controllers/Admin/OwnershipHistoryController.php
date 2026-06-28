<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\Owner;
use App\Models\OwnershipHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OwnershipHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.ownership-histories.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.ownership-histories.store')->only('store');
        $this->middleware('can:admin.ownership-histories.update')->only('update');
        $this->middleware('can:admin.ownership-histories.destroy')->only('destroy');
    }

    public function index(): View
    {
        return view('admin.ownership_histories.index', [
            'cattle' => Cattle::with('breed')
                ->where('status', 'active')
                ->orderBy('code')
                ->get(),
            'owners' => Owner::where('status', 'active')
                ->orderBy('full_name')
                ->get(),
        ]);
    }

    public function list(): JsonResponse
    {
        $histories = OwnershipHistory::query()
            ->with(['cattle.breed', 'owner'])
            ->latest('id');

        return DataTables::eloquent($histories)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (OwnershipHistory $history) => $history->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (OwnershipHistory $history) => $history->cattle?->code ?: '-')
            ->addColumn('owner_name', fn (OwnershipHistory $history) => $this->ownerDisplayName($history->owner) ?: '-')
            ->editColumn('acquisition_type', fn (OwnershipHistory $history) => $this->acquisitionBadge($history->acquisition_type))
            ->editColumn('start_date', fn (OwnershipHistory $history) => $history->start_date?->format('d/m/Y') ?: '-')
            ->editColumn('end_date', fn (OwnershipHistory $history) => $history->end_date?->format('d/m/Y') ?: '-')
            ->editColumn('price', fn (OwnershipHistory $history) => $this->priceLabel($history))
            ->editColumn('is_current', fn (OwnershipHistory $history) => $history->is_current
                ? '<span class="badge badge-success">Actual</span>'
                : '<span class="badge badge-secondary">Historico</span>')
            ->editColumn('created_at', fn (OwnershipHistory $history) => $history->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (OwnershipHistory $history) => view(
                'admin.ownership_histories.partials.acciones',
                compact('history')
            )->render())
            ->rawColumns(['acquisition_type', 'is_current', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $data['is_current'] = $request->boolean('is_current');

        $this->ensureOwnershipDataIsValid($data);

        DB::transaction(function () use ($data): void {
            $history = OwnershipHistory::create($data);
            $this->syncCurrentOwnership($history);
        });

        return response()->json([
            'message' => 'Historial de propietario registrado correctamente.',
        ]);
    }

    public function show(OwnershipHistory $ownershipHistory): JsonResponse
    {
        $ownershipHistory->load(['cattle.breed', 'owner']);

        return response()->json([
            'history' => array_merge($ownershipHistory->toArray(), [
                'cattle_label' => $this->cattleLabel($ownershipHistory->cattle),
                'cattle_code' => $ownershipHistory->cattle?->code,
                'cattle_name' => $ownershipHistory->cattle?->name,
                'cattle_breed_name' => $ownershipHistory->cattle?->breed?->name,
                'cattle_photo_url' => $this->cattlePhotoUrl($ownershipHistory->cattle?->main_photo_path),
                'owner_name' => $this->ownerDisplayName($ownershipHistory->owner),
                'owner_document' => $this->ownerDocument($ownershipHistory->owner),
                'owner_phone' => $ownershipHistory->owner?->phone,
                'owner_email' => $ownershipHistory->owner?->email,
                'acquisition_type_label' => $this->acquisitionLabel($ownershipHistory->acquisition_type),
                'start_date' => $ownershipHistory->start_date?->format('Y-m-d'),
                'end_date' => $ownershipHistory->end_date?->format('Y-m-d'),
                'start_date_formatted' => $ownershipHistory->start_date?->format('d/m/Y'),
                'end_date_formatted' => $ownershipHistory->end_date?->format('d/m/Y') ?: 'Sin fecha de cierre',
                'price_formatted' => $this->priceLabel($ownershipHistory),
                'is_current_label' => $ownershipHistory->is_current ? 'Actual' : 'Historico',
                'created_at_formatted' => $ownershipHistory->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $ownershipHistory->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, OwnershipHistory $ownershipHistory): JsonResponse
    {
        $data = $this->validatedData($request, $ownershipHistory);
        $data['is_current'] = $request->boolean('is_current');

        $this->ensureOwnershipDataIsValid($data, $ownershipHistory);

        DB::transaction(function () use ($ownershipHistory, $data): void {
            $ownershipHistory->update($data);
            $ownershipHistory->refresh();
            $this->syncCurrentOwnership($ownershipHistory);
            $this->ensureCattleHasControlledCurrentOwner((int) $ownershipHistory->cattle_id);
        });

        return response()->json([
            'message' => 'Historial de propietario actualizado correctamente.',
        ]);
    }

    public function destroy(OwnershipHistory $ownershipHistory): JsonResponse
    {
        $cattleId = (int) $ownershipHistory->cattle_id;

        DB::transaction(function () use ($ownershipHistory, $cattleId): void {
            $ownershipHistory->delete();
            $this->ensureCattleHasControlledCurrentOwner($cattleId);
        });

        return response()->json([
            'message' => 'Historial de propietario eliminado correctamente.',
        ]);
    }

    public function create() {}

    public function edit(OwnershipHistory $ownershipHistory) {}

    private function validatedData(Request $request, ?OwnershipHistory $history = null): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'owner_id' => ['required', 'exists:owners,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'acquisition_type' => ['required', Rule::in(array_keys($this->acquisitionTypes()))],
            'document_reference' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'is_current' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'owner_id.required' => 'Seleccione el propietario.',
            'owner_id.exists' => 'El propietario seleccionado no es valido.',
            'start_date.required' => 'Ingrese la fecha desde.',
            'start_date.date' => 'La fecha desde no es valida.',
            'end_date.date' => 'La fecha hasta no es valida.',
            'end_date.after_or_equal' => 'La fecha hasta no puede ser menor que la fecha desde.',
            'acquisition_type.required' => 'Seleccione el tipo de adquisicion.',
            'acquisition_type.in' => 'El tipo de adquisicion seleccionado no es valido.',
            'document_reference.max' => 'La referencia de documento no debe superar 255 caracteres.',
            'price.numeric' => 'El precio debe ser numerico.',
            'price.min' => 'El precio no puede ser negativo.',
        ]);
    }

    private function ensureOwnershipDataIsValid(array $data, ?OwnershipHistory $history = null): void
    {
        $this->ensureExactDuplicateDoesNotExist($data, $history);
        $this->ensureDateRangeDoesNotOverlap($data, $history);
    }

    private function ensureExactDuplicateDoesNotExist(array $data, ?OwnershipHistory $history = null): void
    {
        $exists = OwnershipHistory::query()
            ->where('cattle_id', $data['cattle_id'])
            ->where('owner_id', $data['owner_id'])
            ->whereDate('start_date', $data['start_date'])
            ->when($history, fn ($query) => $query->whereKeyNot($history->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'start_date' => 'Este propietario ya tiene un historial registrado para este ganado en esa fecha.',
            ]);
        }
    }

    private function ensureDateRangeDoesNotOverlap(array $data, ?OwnershipHistory $history = null): void
    {
        $startDate = Carbon::parse($data['start_date'])->toDateString();
        $endDate = ! empty($data['end_date'])
            ? Carbon::parse($data['end_date'])->toDateString()
            : '9999-12-31';

        $overlap = OwnershipHistory::query()
            ->where('cattle_id', $data['cattle_id'])
            ->when($history, fn ($query) => $query->whereKeyNot($history->id))
            ->when((bool) ($data['is_current'] ?? false), fn ($query) => $query->where('is_current', false))
            ->whereDate('start_date', '<=', $endDate)
            ->where(function ($query) use ($startDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $startDate);
            })
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'start_date' => 'El rango de fechas se superpone con otro historial del mismo ganado.',
            ]);
        }
    }

    private function syncCurrentOwnership(OwnershipHistory $history): void
    {
        if (! $history->is_current) {
            return;
        }

        $startDate = Carbon::parse($history->start_date);

        $previousCurrent = OwnershipHistory::query()
            ->where('cattle_id', $history->cattle_id)
            ->whereKeyNot($history->id)
            ->where('is_current', true)
            ->get();

        foreach ($previousCurrent as $previous) {
            $endDate = $startDate->copy()->subDay();

            if ($endDate->lt(Carbon::parse($previous->start_date))) {
                $endDate = $startDate->copy();
            }

            $previous->update([
                'is_current' => false,
                'end_date' => $previous->end_date ?: $endDate->toDateString(),
            ]);
        }

        $history->update([
            'is_current' => true,
            'end_date' => null,
        ]);

        Cattle::whereKey($history->cattle_id)->update([
            'current_owner_id' => $history->owner_id,
        ]);
    }

    private function ensureCattleHasControlledCurrentOwner(int $cattleId): void
    {
        $current = OwnershipHistory::query()
            ->where('cattle_id', $cattleId)
            ->where('is_current', true)
            ->latest('start_date')
            ->latest('id')
            ->first();

        if (! $current) {
            $current = OwnershipHistory::query()
                ->where('cattle_id', $cattleId)
                ->latest('start_date')
                ->latest('id')
                ->first();

            if ($current) {
                OwnershipHistory::query()
                    ->where('cattle_id', $cattleId)
                    ->whereKeyNot($current->id)
                    ->update(['is_current' => false]);

                $current->update([
                    'is_current' => true,
                    'end_date' => null,
                ]);
            }
        }

        Cattle::whereKey($cattleId)->update([
            'current_owner_id' => $current?->owner_id,
        ]);
    }

    private function acquisitionTypes(): array
    {
        return [
            'birth' => 'Nacimiento',
            'purchase' => 'Compra',
            'sale' => 'Venta',
            'transfer' => 'Transferencia',
            'donation' => 'Donacion',
            'other' => 'Otro',
        ];
    }

    private function acquisitionLabel(?string $type): string
    {
        return $this->acquisitionTypes()[$type] ?? '-';
    }

    private function acquisitionBadge(?string $type): string
    {
        $classes = [
            'birth' => 'badge-success',
            'purchase' => 'badge-primary',
            'sale' => 'badge-info',
            'transfer' => 'badge-warning',
            'donation' => 'badge-light border',
            'other' => 'badge-secondary',
        ];

        return '<span class="badge '.($classes[$type] ?? 'badge-secondary').'">'.$this->acquisitionLabel($type).'</span>';
    }

    private function priceLabel(OwnershipHistory $history): string
    {
        if ($history->price === null) {
            return '-';
        }

        return trim(($history->currency ?: 'PEN').' '.number_format((float) $history->price, 2));
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

    private function ownerDocument(?Owner $owner): string
    {
        if (! $owner?->document_number) {
            return '-';
        }

        return trim(($owner->document_type ? $owner->document_type.' ' : '').$owner->document_number);
    }

    private function cattlePhotoUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
