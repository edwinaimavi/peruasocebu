@extends('layouts.app')

@section('subtitle', 'Historial Reproductivo')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-venus-mars"></i>
                </span>
                <div>
                    <h1 class="module-title">Historial Reproductivo</h1>
                    <p class="module-subtitle">
                        Administra cruces, inseminaciones, controles de prenez, partos y crias del ganado.
                    </p>
                </div>
            </div>

            @can('admin.reproduction-records.store')
                <button class="btn btn-create" id="newReproductionRecordButton" type="button" data-toggle="modal"
                    data-target="#reproductionRecordModal">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Historial Reproductivo</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableReproductionRecord" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Animal principal</th>
                            <th>Codigo</th>
                            <th>Pareja</th>
                            <th>Metodo</th>
                            <th>Fecha</th>
                            <th>Resultado prenez</th>
                            <th>Fecha parto</th>
                            <th>Cria</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.reproduction_records.partials.modal')
    @include('admin.reproduction_records.partials.detail-modal')
@stop

@push('css')
    <style>
        #reproductionRecordModal .modal-dialog,
        #reproductionRecordDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1040px;
            margin: 20px auto;
        }

        #reproductionRecordModal .modal-content,
        #reproductionRecordDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #reproductionRecordModal .reproduction-record-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #reproductionRecordModal .modal-body,
        #reproductionRecordDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .reproduction-section-title {
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

        .reproduction-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .reproduction-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .reproduction-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .reproduction-detail-photo-wrap {
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

        .reproduction-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .reproduction-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .reproduction-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reproduction-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .reproduction-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #reproductionRecordModal .modal-dialog,
            #reproductionRecordDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #reproductionRecordModal .modal-content,
            #reproductionRecordDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .reproduction-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.reproductionRecordRoutes = {
            index: @json(route('admin.reproduction-records.index')),
            list: @json(route('admin.reproduction-records.list')),
        };
    </script>
    @vite(['resources/js/pages/reproduction-record.js'])
@endpush
