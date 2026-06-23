@extends('layouts.app')

@section('subtitle', 'Kardex')

@section('content_body')

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="mb-3">
            <h4 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-boxes text-primary mr-2"></i>
                Kardex de Productos
            </h4>
            <small class="text-muted">Control de movimientos de inventario</small>
        </div>

        <div class="card shadow border-0 rounded-lg">

            {{-- FILTRO --}}
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">

                    <div class="col-md-4">
                        <label class="font-weight-bold text-muted small mb-1">
                            <i class="fas fa-filter mr-1"></i> Filtrar por producto
                        </label>

                        <select id="filterProduct" class="form-control form-control-sm shadow-sm">
                            <option value="">-- Todos los productos --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- TABLA --}}
            <div class="card-body p-2">

                <div class="table-responsive">
                    <table id="kardexTable" class="table table-hover align-middle text-center mb-0">

                        <thead class="bg-light text-uppercase small text-secondary">
                            <tr>
                                <th width="5%">#</th>
                                <th>Producto</th>
                                <th width="12%">Movimiento</th>
                                <th width="12%">Cantidad</th>
                                <th width="12%">Saldo</th>
                                <th width="12%">Origen</th>
                                <th width="10%">Ref.</th>
                                <th width="15%">Fecha</th>
                            </tr>
                        </thead>

                    </table>
                </div>

            </div>

        </div>

    </div>

@stop


@push('css')
    <style>
        /* 🔥 mejora visual tabla */
        #kardexTable tbody tr:hover {
            background: #f4f6f9;
            transition: 0.2s;
        }

        .badge {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 6px;
        }

        .card {
            border-radius: 12px;
        }

        .card-header {
            border-radius: 12px 12px 0 0;
        }

        select.form-control {
            border-radius: 8px;
        }
    </style>
@endpush


@push('js')
    <script>
        $(function() {

            let table = $('#kardexTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,

                ajax: {
                    url: "{{ route('admin.stock.kardex') }}",
                    data: function(d) {
                        d.product_id = $('#filterProduct').val();
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'quantity_format',
                        name: 'quantity'
                    }, {
                        data: 'saldo_format',
                        name: 'saldo'
                    },
                    {
                        data: 'source',
                        name: 'source'
                    },
                    {
                        data: 'reference_id',
                        name: 'reference_id'
                    },
                    {
                        data: 'date',
                        name: 'created_at'
                    }
                ],

                language: {
                    processing: "Procesando...",
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ movimientos",
                    infoEmpty: "Sin registros",
                    infoFiltered: "(filtrado de _MAX_ total)",
                    loadingRecords: "Cargando...",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "No hay movimientos registrados",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                }

            });

            // 🔥 FILTRO DINÁMICO
            $('#filterProduct').change(function() {
                table.ajax.reload();
            });

        });
    </script>
@endpush
