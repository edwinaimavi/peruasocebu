<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cattle;
use App\Models\CattleSale;
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

class CattleSaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.cattle-sales.index')->only('index', 'list', 'show');
        $this->middleware('can:admin.cattle-sales.store')->only('store');
        $this->middleware('can:admin.cattle-sales.update')->only('update');
        $this->middleware('can:admin.cattle-sales.destroy')->only('destroy');
    }

    public function index(): View
    {
        $cattle = Cattle::with(['breed', 'currentOwner'])
            ->where('status', 'active')
            ->orderBy('code')
            ->get();

        return view('admin.cattle_sales.index', [
            'cattle' => $cattle,
            'cattleOptions' => $cattle->map(fn (Cattle $animal) => [
                'id' => $animal->id,
                'current_owner_id' => $animal->current_owner_id,
                'current_owner_name' => $this->ownerDisplayName($animal->currentOwner),
            ])->values(),
            'owners' => Owner::where('status', 'active')->orderBy('full_name')->get(),
        ]);
    }

    public function list(): JsonResponse
    {
        $sales = CattleSale::query()
            ->with(['cattle.breed', 'seller', 'buyer'])
            ->latest('id');

        return DataTables::eloquent($sales)
            ->addIndexColumn()
            ->addColumn('cattle_name', fn (CattleSale $sale) => $sale->cattle?->name ?: '-')
            ->addColumn('cattle_code', fn (CattleSale $sale) => $sale->cattle?->code ?: '-')
            ->addColumn('seller_name', fn (CattleSale $sale) => $this->ownerDisplayName($sale->seller) ?: '-')
            ->addColumn('buyer_name', fn (CattleSale $sale) => $this->ownerDisplayName($sale->buyer) ?: '-')
            ->editColumn('sale_date', fn (CattleSale $sale) => $sale->sale_date?->format('d/m/Y') ?: '-')
            ->editColumn('sale_price', fn (CattleSale $sale) => $this->priceLabel($sale))
            ->editColumn('payment_method', fn (CattleSale $sale) => $this->paymentBadge($sale->payment_method))
            ->editColumn('status', fn (CattleSale $sale) => $this->statusBadge($sale->status))
            ->editColumn('created_at', fn (CattleSale $sale) => $sale->created_at?->format('d/m/Y H:i'))
            ->addColumn('acciones', fn (CattleSale $sale) => view(
                'admin.cattle_sales.partials.acciones',
                compact('sale')
            )->render())
            ->rawColumns(['payment_method', 'status', 'acciones'])
            ->toJson();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $this->ensureSaleDataIsValid($data);
        $uploadedPath = $this->storeContract($request);

        try {
            DB::transaction(function () use ($data, $uploadedPath): void {
                $sale = CattleSale::create(array_merge($data, $uploadedPath));
                $this->syncCompletedSale($sale);
            });
        } catch (\Throwable $exception) {
            $this->deleteContract($uploadedPath['contract_file_path'] ?? null);
            throw $exception;
        }

        return response()->json([
            'message' => 'Venta de ganado registrada correctamente.',
        ]);
    }

    public function show(CattleSale $cattleSale): JsonResponse
    {
        $cattleSale->load(['cattle.breed', 'seller', 'buyer']);

        return response()->json([
            'sale' => array_merge($cattleSale->toArray(), [
                'cattle_label' => $this->cattleLabel($cattleSale->cattle),
                'cattle_code' => $cattleSale->cattle?->code,
                'cattle_name' => $cattleSale->cattle?->name,
                'cattle_breed_name' => $cattleSale->cattle?->breed?->name,
                'cattle_sale_status_label' => $this->cattleSaleStatusLabel($cattleSale->cattle?->sale_status),
                'cattle_photo_url' => $this->fileUrl($cattleSale->cattle?->main_photo_path),
                'seller_name' => $this->ownerDisplayName($cattleSale->seller),
                'buyer_name' => $this->ownerDisplayName($cattleSale->buyer),
                'sale_date' => $cattleSale->sale_date?->format('Y-m-d'),
                'sale_date_formatted' => $cattleSale->sale_date?->format('d/m/Y'),
                'sale_price_formatted' => $this->priceLabel($cattleSale),
                'payment_method_label' => $this->paymentLabel($cattleSale->payment_method),
                'status_label' => $this->statusLabel($cattleSale->status),
                'contract_file_url' => $this->fileUrl($cattleSale->contract_file_path),
                'contract_file_name' => $cattleSale->contract_file_path ? basename($cattleSale->contract_file_path) : null,
                'created_at_formatted' => $cattleSale->created_at?->format('d/m/Y H:i'),
                'updated_at_formatted' => $cattleSale->updated_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function update(Request $request, CattleSale $cattleSale): JsonResponse
    {
        $data = $this->validatedData($request, $cattleSale);
        $this->ensureSaleDataIsValid($data, $cattleSale);
        $uploadedPath = $this->storeContract($request);
        $oldContractPath = $uploadedPath ? $cattleSale->contract_file_path : null;
        $wasCompleted = $cattleSale->status === 'completed';

        try {
            DB::transaction(function () use ($cattleSale, $data, $uploadedPath, $wasCompleted): void {
                $cattleSale->update(array_merge($data, $uploadedPath));
                $cattleSale->refresh();

                if ($cattleSale->status === 'completed') {
                    $this->syncCompletedSale($cattleSale);
                    return;
                }

                if ($wasCompleted && $cattleSale->status === 'cancelled') {
                    $this->revertCompletedSale($cattleSale);
                }
            });
        } catch (\Throwable $exception) {
            $this->deleteContract($uploadedPath['contract_file_path'] ?? null);
            throw $exception;
        }

        $this->deleteContract($oldContractPath);

        return response()->json([
            'message' => 'Venta de ganado actualizada correctamente.',
        ]);
    }

    public function destroy(CattleSale $cattleSale): JsonResponse
    {
        DB::transaction(function () use ($cattleSale): void {
            $wasCompleted = $cattleSale->status === 'completed';
            $cattleSale->delete();

            if ($wasCompleted) {
                $this->revertCompletedSale($cattleSale);
            }
        });

        return response()->json([
            'message' => 'Venta de ganado eliminada correctamente.',
        ]);
    }

    public function create() {}

    public function edit(CattleSale $cattleSale) {}

    private function validatedData(Request $request, ?CattleSale $sale = null): array
    {
        return $request->validate([
            'cattle_id' => ['required', 'exists:cattle,id'],
            'seller_owner_id' => ['nullable', 'exists:owners,id'],
            'buyer_owner_id' => ['required', 'exists:owners,id'],
            'sale_date' => ['required', 'date'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'payment_method' => ['required', Rule::in(array_keys($this->paymentMethods()))],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:5120'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ], [
            'cattle_id.required' => 'Seleccione el ganado.',
            'cattle_id.exists' => 'El ganado seleccionado no es valido.',
            'buyer_owner_id.required' => 'Seleccione el comprador.',
            'buyer_owner_id.exists' => 'El comprador seleccionado no es valido.',
            'seller_owner_id.exists' => 'El vendedor seleccionado no es valido.',
            'sale_date.required' => 'Ingrese la fecha de venta.',
            'sale_date.date' => 'La fecha de venta no es valida.',
            'sale_price.required' => 'Ingrese el precio de venta.',
            'sale_price.numeric' => 'El precio de venta debe ser numerico.',
            'sale_price.min' => 'El precio de venta no puede ser negativo.',
            'currency.required' => 'Seleccione la moneda.',
            'payment_method.required' => 'Seleccione el metodo de pago.',
            'payment_method.in' => 'El metodo de pago seleccionado no es valido.',
            'contract_file.file' => 'El contrato debe ser un archivo valido.',
            'contract_file.mimes' => 'El contrato debe ser PDF, imagen o Word.',
            'contract_file.max' => 'El contrato no debe superar los 5 MB.',
            'status.required' => 'Seleccione el estado de la venta.',
            'status.in' => 'El estado seleccionado no es valido.',
        ]);
    }

    private function ensureSaleDataIsValid(array $data, ?CattleSale $sale = null): void
    {
        if (
            ! empty($data['seller_owner_id'])
            && (int) $data['seller_owner_id'] === (int) $data['buyer_owner_id']
        ) {
            throw ValidationException::withMessages([
                'buyer_owner_id' => 'El vendedor y el comprador no pueden ser el mismo propietario.',
            ]);
        }

        if (($data['status'] ?? null) !== 'completed') {
            return;
        }

        $exists = CattleSale::query()
            ->where('cattle_id', $data['cattle_id'])
            ->where('status', 'completed')
            ->when($sale, fn ($query) => $query->whereKeyNot($sale->id))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'status' => 'Este ganado ya tiene una venta completada registrada.',
            ]);
        }
    }

    private function syncCompletedSale(CattleSale $sale): void
    {
        $sale->loadMissing('cattle');

        $sale->cattle?->update([
            'current_owner_id' => $sale->buyer_owner_id,
            'sale_status' => 'sold',
        ]);

        $this->closeCurrentOwnershipHistories($sale);

        OwnershipHistory::updateOrCreate([
            'cattle_id' => $sale->cattle_id,
            'owner_id' => $sale->buyer_owner_id,
            'start_date' => $sale->sale_date,
            'acquisition_type' => 'purchase',
        ], [
            'end_date' => null,
            'document_reference' => $sale->contract_file_path,
            'price' => $sale->sale_price,
            'currency' => $sale->currency,
            'is_current' => true,
            'notes' => 'Venta registrada desde el modulo ventas.',
        ]);
    }

    private function closeCurrentOwnershipHistories(CattleSale $sale): void
    {
        $saleDate = Carbon::parse($sale->sale_date);

        $currentHistories = OwnershipHistory::query()
            ->where('cattle_id', $sale->cattle_id)
            ->where('is_current', true)
            ->where('owner_id', '!=', $sale->buyer_owner_id)
            ->get();

        foreach ($currentHistories as $history) {
            $endDate = $saleDate->copy()->subDay();

            if ($history->start_date && $endDate->lt(Carbon::parse($history->start_date))) {
                $endDate = $saleDate->copy();
            }

            $history->update([
                'is_current' => false,
                'end_date' => $history->end_date ?: $endDate->toDateString(),
            ]);
        }
    }

    private function revertCompletedSale(CattleSale $sale): void
    {
        $generatedHistory = OwnershipHistory::query()
            ->where('cattle_id', $sale->cattle_id)
            ->where('owner_id', $sale->buyer_owner_id)
            ->whereDate('start_date', $sale->sale_date)
            ->where('acquisition_type', 'purchase')
            ->where('is_current', true)
            ->first();

        if ($generatedHistory) {
            $generatedHistory->update([
                'is_current' => false,
                'end_date' => $sale->sale_date,
            ]);
        }

        $current = OwnershipHistory::query()
            ->where('cattle_id', $sale->cattle_id)
            ->where('is_current', true)
            ->latest('start_date')
            ->latest('id')
            ->first();

        if (! $current) {
            $current = OwnershipHistory::query()
                ->where('cattle_id', $sale->cattle_id)
                ->where(function ($query) use ($sale) {
                    $query->where('owner_id', '!=', $sale->buyer_owner_id)
                        ->orWhereNull('owner_id');
                })
                ->latest('start_date')
                ->latest('id')
                ->first();

            $current?->update([
                'is_current' => true,
                'end_date' => null,
            ]);
        }

        Cattle::whereKey($sale->cattle_id)->update([
            'current_owner_id' => $current?->owner_id,
            'sale_status' => 'available',
        ]);
    }

    private function storeContract(Request $request): array
    {
        if (! $request->hasFile('contract_file')) {
            return [];
        }

        return [
            'contract_file_path' => $request->file('contract_file')
                ->store('cattle/sales/contracts', 'public'),
        ];
    }

    private function deleteContract(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
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

    private function cattleLabel(?Cattle $cattle): string
    {
        if (! $cattle) {
            return '-';
        }

        return trim($cattle->code.' - '.($cattle->name ?: 'Sin nombre'));
    }

    private function priceLabel(CattleSale $sale): string
    {
        if ($sale->sale_price === null) {
            return '-';
        }

        return trim(($sale->currency ?: 'PEN').' '.number_format((float) $sale->sale_price, 2));
    }

    private function paymentMethods(): array
    {
        return [
            'cash' => 'Efectivo',
            'transfer' => 'Transferencia',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'deposit' => 'Deposito',
            'other' => 'Otro',
        ];
    }

    private function statuses(): array
    {
        return [
            'pending' => 'Pendiente',
            'completed' => 'Completado',
            'cancelled' => 'Anulado',
        ];
    }

    private function paymentLabel(?string $method): string
    {
        return $this->paymentMethods()[$method] ?? '-';
    }

    private function statusLabel(?string $status): string
    {
        return $this->statuses()[$status] ?? '-';
    }

    private function paymentBadge(?string $method): string
    {
        return '<span class="badge badge-light border">'.$this->paymentLabel($method).'</span>';
    }

    private function statusBadge(?string $status): string
    {
        $classes = [
            'pending' => 'badge-warning',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];

        return '<span class="badge '.($classes[$status] ?? 'badge-secondary').'">'.$this->statusLabel($status).'</span>';
    }

    private function cattleSaleStatusLabel(?string $status): string
    {
        return match ($status) {
            'available' => 'Disponible',
            'reserved' => 'Reservado',
            'sold' => 'Vendido',
            'not_available' => 'No disponible',
            default => '-',
        };
    }

    private function fileUrl(?string $path): ?string
    {
        return $path ? Storage::disk('public')->url($path) : null;
    }
}
