@extends('layouts.app')

@section('subtitle', 'Revisiones Veterinarias')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-notes-medical"></i>
                </span>
                <div>
                    <h1 class="module-title">Revisiones Veterinarias</h1>
                    <p class="module-subtitle">
                        Administra atenciones veterinarias, diagnosticos, tratamientos y controles sanitarios del ganado.
                    </p>
                </div>
            </div>

            @can('admin.veterinary-records.store')
                <button class="btn btn-create" id="newVeterinaryRecordButton" type="button" data-toggle="modal"
                    data-target="#veterinaryRecordModal">
                    <i class="fas fa-plus"></i> Nueva Revision
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Revisiones Veterinarias</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableVeterinaryRecord" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Veterinario</th>
                            <th>Tipo</th>
                            <th>Fecha atencion</th>
                            <th>Proxima visita</th>
                            <th>Archivo</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.veterinary_records.partials.modal')
    @include('admin.veterinary_records.partials.detail-modal')
@stop

@push('css')
    <style>
        #veterinaryRecordModal .modal-dialog,
        #veterinaryRecordDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #veterinaryRecordModal .modal-content,
        #veterinaryRecordDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #veterinaryRecordModal .veterinary-record-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #veterinaryRecordModal .modal-body,
        #veterinaryRecordDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .veterinary-section-title {
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

        .veterinary-file-card {
            align-items: center;
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 12px;
            display: flex;
            gap: 16px;
            padding: 16px;
        }

        .veterinary-file-icon {
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

        .veterinary-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .veterinary-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .veterinary-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .veterinary-detail-photo-wrap {
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

        .veterinary-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .veterinary-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .veterinary-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .veterinary-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .veterinary-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #veterinaryRecordModal .modal-dialog,
            #veterinaryRecordDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #veterinaryRecordModal .modal-content,
            #veterinaryRecordDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .veterinary-file-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .veterinary-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.veterinaryRecordRoutes = {
            index: @json(route('admin.veterinary-records.index')),
            list: @json(route('admin.veterinary-records.list')),
        };
    </script>
    @vite(['resources/js/pages/veterinary-record.js'])
@endpush
