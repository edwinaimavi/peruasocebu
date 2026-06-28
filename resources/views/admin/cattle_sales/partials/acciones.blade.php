<div class="d-flex justify-content-center align-items-center">
    @can('admin.cattle-sales.index')
        <button type="button" class="btn btn-outline-primary btn-xs viewCattleSale mr-1" title="Ver detalle"
            data-id="{{ $sale->id }}">
            <i class="fas fa-eye"></i>
        </button>
    @endcan

    @can('admin.cattle-sales.update')
        <button type="button" class="btn btn-outline-info btn-xs editCattleSale mr-1" title="Editar venta"
            data-id="{{ $sale->id }}">
            <i class="fas fa-pen"></i>
        </button>
    @endcan

    @can('admin.cattle-sales.destroy')
        <button type="button" class="btn btn-outline-danger btn-xs deleteCattleSale" title="Eliminar venta"
            data-id="{{ $sale->id }}"
            data-name="{{ $sale->cattle?->code }} - {{ $sale->buyer?->business_name ?: $sale->buyer?->full_name }}">
            <i class="fas fa-trash"></i>
        </button>
    @endcan
</div>
