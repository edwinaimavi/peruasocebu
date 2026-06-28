@extends('layouts.app')

@section('subtitle', 'Historial de Propietarios')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-history"></i>
                </span>
                <div>
                    <h1 class="module-title">Historial de Propietarios</h1>
                    <p class="module-subtitle">
                        Administra los cambios de propietario del ganado, fechas de posesion, documentos y tipo de adquisicion.
                    </p>
                </div>
            </div>

            @can('admin.ownership-histories.store')
                <button class="btn btn-create" id="newOwnershipHistoryButton" type="button" data-toggle="modal"
                    data-target="#ownershipHistoryModal">
                    <i class="fas fa-plus"></i> Nuevo Historial
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Historial de Propietarios</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableOwnershipHistory" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Propietario</th>
                            <th>Tipo adquisicion</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Precio</th>
                            <th>Actual</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.ownership_histories.partials.modal')
    @include('admin.ownership_histories.partials.detail-modal')
@stop

@push('css')
    <style>
        #ownershipHistoryModal .modal-dialog,
        #ownershipHistoryDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #ownershipHistoryModal .modal-content,
        #ownershipHistoryDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #ownershipHistoryModal .ownership-history-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #ownershipHistoryModal .modal-body,
        #ownershipHistoryDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #ownershipHistoryModal .modal-footer,
        #ownershipHistoryDetailModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        .ownership-section-title {
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

        .ownership-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .ownership-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .ownership-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .ownership-detail-photo-wrap {
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

        .ownership-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .ownership-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .ownership-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ownership-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .ownership-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #ownershipHistoryModal .modal-dialog,
            #ownershipHistoryDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #ownershipHistoryModal .modal-content,
            #ownershipHistoryDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .ownership-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.ownershipHistoryRoutes = {
            index: @json(route('admin.ownership-histories.index')),
            list: @json(route('admin.ownership-histories.list')),
        };
    </script>
    @vite(['resources/js/pages/ownership-history.js'])
@endpush
