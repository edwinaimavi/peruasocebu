@extends('layouts.app')

@section('subtitle', 'Ventas del Ganado')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-handshake"></i>
                </span>
                <div>
                    <h1 class="module-title">Ventas del Ganado</h1>
                    <p class="module-subtitle">
                        Administra las operaciones de venta, compradores, vendedores, contratos y cambios de propietario del ganado.
                    </p>
                </div>
            </div>

            @can('admin.cattle-sales.store')
                <button class="btn btn-create" id="newCattleSaleButton" type="button" data-toggle="modal"
                    data-target="#cattleSaleModal">
                    <i class="fas fa-plus"></i> Nueva Venta
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Ventas del Ganado</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableCattleSale" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Vendedor</th>
                            <th>Comprador</th>
                            <th>Fecha</th>
                            <th>Precio</th>
                            <th>Pago</th>
                            <th>Estado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.cattle_sales.partials.modal')
    @include('admin.cattle_sales.partials.detail-modal')
@stop

@push('css')
    <style>
        #cattleSaleModal .modal-dialog,
        #cattleSaleDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #cattleSaleModal .modal-content,
        #cattleSaleDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #cattleSaleModal .cattle-sale-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #cattleSaleModal .modal-body,
        #cattleSaleDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sale-section-title {
            align-items: center;
            color: #1f4d36;
            display: flex;
            font-size: .9rem;
            font-weight: 800;
            gap: 8px;
            letter-spacing: .02em;
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .sale-file-card {
            align-items: center;
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 12px;
            display: flex;
            gap: 16px;
            padding: 16px;
        }

        .sale-file-icon {
            align-items: center;
            background: #edf3ee;
            border-radius: 12px;
            color: #1f4d36;
            display: flex;
            flex: 0 0 72px;
            font-size: 2rem;
            height: 72px;
            justify-content: center;
            width: 72px;
        }

        .sale-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .sale-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .sale-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .sale-detail-photo-wrap {
            align-items: center;
            background: #fff;
            border: 4px solid #fff;
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
            display: inline-flex;
            height: 112px;
            justify-content: center;
            overflow: hidden;
            width: 112px;
        }

        .sale-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .sale-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .sale-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sale-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .sale-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #cattleSaleModal .modal-dialog,
            #cattleSaleDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #cattleSaleModal .modal-content,
            #cattleSaleDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .sale-file-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .sale-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.cattleSaleRoutes = {
            index: @json(route('admin.cattle-sales.index')),
            list: @json(route('admin.cattle-sales.list')),
        };
        window.cattleSaleCattle = @json($cattleOptions);
    </script>
    @vite(['resources/js/pages/cattle-sale.js'])
@endpush
