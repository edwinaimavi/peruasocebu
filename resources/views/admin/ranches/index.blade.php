@extends('layouts.app')

@section('subtitle', 'Criaderos / Haciendas')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-warehouse"></i>
                </span>
                <div>
                    <h1 class="module-title">Criaderos / Haciendas</h1>
                    <p class="module-subtitle">
                        Administra la información institucional de tus criaderos, haciendas y centros ganaderos.
                    </p>
                </div>
            </div>

            @can('admin.ranches.store')
                <button class="btn btn-create" id="newRanchButton" type="button" data-toggle="modal"
                    data-target="#ranchModal">
                    <i class="fas fa-plus"></i> Nuevo Criadero
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Criaderos / Haciendas</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableRanch" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Razón social</th>
                            <th>Documento</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.ranches.partials.modal')
    @include('admin.ranches.partials.detail-modal')
@stop

@push('css')
    <style>
        #ranchModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1140px;
            margin: 20px auto;
        }

        #ranchModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #ranchModal .ranch-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #ranchModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #ranchModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
            padding: 12px 16px;
        }

        .ranch-file-card {
            border: 1px dashed #d7dee5;
            border-radius: 10px;
            background: #fff;
            padding: 10px;
        }

        .ranch-file-preview {
            width: 100%;
            max-height: 120px;
            object-fit: contain;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #edf0f2;
        }

        .ranch-file-preview--logo {
            height: 130px;
        }

        .ranch-file-preview--compact {
            height: 90px;
        }

        #ranchModal .ranch-file-placeholder {
            padding-bottom: .65rem !important;
            padding-top: .65rem !important;
        }

        @media (max-width: 991.98px) {
            #ranchModal .modal-dialog {
                margin: 12px auto;
            }

            #ranchModal .modal-content {
                max-height: calc(100vh - 24px);
            }
        }

        @media (max-width: 575.98px) {
            #ranchModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #ranchModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            #ranchModal .modal-header,
            #ranchModal .modal-body,
            #ranchModal .modal-footer {
                padding-left: 12px;
                padding-right: 12px;
            }
        }

        .ranch-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .ranch-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .ranch-detail-media {
            height: 150px;
            width: 100%;
            object-fit: contain;
            background: #f8fafc;
            border: 1px solid #edf0f2;
            border-radius: 10px;
        }
    </style>
@endpush

@push('js')
    <script>
        window.ranchRoutes = {
            index: @json(route('admin.ranches.index')),
            list: @json(route('admin.ranches.list')),
            consultDocument: @json(route('admin.documents.consult', ['numero' => '__NUMBER__'])),
        };
    </script>
    @vite(['resources/js/pages/ranch.js'])
@endpush
