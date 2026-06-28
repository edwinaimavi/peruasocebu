@extends('layouts.app')

@section('subtitle', 'Historial de Pesajes')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-weight"></i>
                </span>
                <div>
                    <h1 class="module-title">Historial de Pesajes</h1>
                    <p class="module-subtitle">
                        Administra el control de peso, condicion corporal y evolucion fisica del ganado.
                    </p>
                </div>
            </div>

            @can('admin.weight-records.store')
                <button class="btn btn-create" id="newWeightRecordButton" type="button" data-toggle="modal"
                    data-target="#weightRecordModal">
                    <i class="fas fa-plus"></i> Nuevo Pesaje
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Historial de Pesajes</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableWeightRecord" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Peso</th>
                            <th>Fecha pesaje</th>
                            <th>Condicion corporal</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.weight_records.partials.modal')
    @include('admin.weight_records.partials.detail-modal')
@stop

@push('css')
    <style>
        #weightRecordModal .modal-dialog,
        #weightRecordDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #weightRecordModal .modal-content,
        #weightRecordDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #weightRecordModal .weight-record-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #weightRecordModal .modal-body,
        #weightRecordDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .weight-section-title {
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

        .weight-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .weight-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .weight-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .weight-detail-photo-wrap {
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

        .weight-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .weight-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .weight-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .weight-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .weight-detail-item-wide {
            grid-column: 1 / -1;
        }

        .badge-orange {
            background-color: #fd7e14;
            color: #fff;
        }

        @media (max-width: 575.98px) {
            #weightRecordModal .modal-dialog,
            #weightRecordDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #weightRecordModal .modal-content,
            #weightRecordDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .weight-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.weightRecordRoutes = {
            index: @json(route('admin.weight-records.index')),
            list: @json(route('admin.weight-records.list')),
        };
    </script>
    @vite(['resources/js/pages/weight-record.js'])
@endpush
