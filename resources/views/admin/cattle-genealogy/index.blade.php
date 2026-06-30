@extends('layouts.app')

@section('subtitle', 'Genealogía del Ganado')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-sitemap"></i>
                </span>
                <div>
                    <h1 class="module-title">Genealogía del Ganado</h1>
                    <p class="module-subtitle">
                        Administra las relaciones familiares del ganado para rastrear linaje, pureza racial y generaciones.
                    </p>
                </div>
            </div>

            @can('admin.cattle-genealogy.store')
                <button class="btn btn-create" id="newGenealogyButton" type="button" data-toggle="modal"
                    data-target="#genealogyModal">
                    <i class="fas fa-plus"></i> Nuevo Registro Genealógico
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Genealogía del Ganado</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableGenealogy" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Animal principal</th>
                            <th>Código animal</th>
                            <th>Relación</th>
                            <th>Generacion</th>
                            <th>Familiar</th>
                            <th>Raza familiar</th>
                            <th>Pureza</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.cattle-genealogy.partials.modal')
    @include('admin.cattle-genealogy.partials.detail-modal')
@stop

@push('css')
    <style>
        #genealogyModal .modal-dialog,
        #genealogyDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #genealogyModal .modal-content,
        #genealogyDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #genealogyModal .genealogy-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #genealogyModal .modal-body,
        #genealogyDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #genealogyModal .modal-footer,
        #genealogyDetailModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        .genealogy-section-title {
            align-items: center;
            color: #1f4d36;
            display: flex;
            font-size: .82rem;
            font-weight: 800;
            gap: 8px;
            letter-spacing: .04em;
            margin: 4px 0 14px;
            text-transform: uppercase;
        }

        .genealogy-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .genealogy-detail-value {
            color: #2f3b43;
            min-height: 24px;
            overflow-wrap: break-word;
            white-space: normal;
            word-break: break-word;
        }

        .genealogy-detail-hero,
        .genealogy-flow-card {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .genealogy-detail-photo-wrap {
            align-items: center;
            background: #fff;
            border: 4px solid #fff;
            border-radius: 18px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
            display: inline-flex;
            flex: 0 0 auto;
            height: 120px;
            justify-content: center;
            overflow: hidden;
            width: 140px;
        }

        .genealogy-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .genealogy-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .genealogy-flow-grid {
            align-items: stretch;
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
        }

        .genealogy-flow-node {
            background: #fff;
            border: 1px solid #e6eaee;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(31, 77, 54, .06);
            min-width: 0;
            padding: 14px;
        }

        .genealogy-flow-relation {
            align-items: center;
            color: #795b20;
            display: flex;
            flex-direction: column;
            font-weight: 800;
            justify-content: center;
            min-width: 0;
            text-align: center;
        }

        .genealogy-detail-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .genealogy-detail-item {
            min-width: 0;
        }

        .genealogy-detail-item-wide {
            grid-column: 1 / -1;
        }

        .genealogy-lineage-help,
        .genealogy-relation-preview {
            background: #f8fbf8;
            border: 1px solid rgba(31, 77, 54, .12);
            border-radius: 12px;
            color: #2f3b43;
            font-size: .82rem;
            line-height: 1.45;
            padding: 10px 12px;
        }

        .genealogy-relation-preview {
            margin-top: 8px;
        }

        .genealogy-relation-preview-title {
            align-items: center;
            color: #1f4d36;
            display: flex;
            font-size: .75rem;
            font-weight: 900;
            gap: 6px;
            letter-spacing: .04em;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .genealogy-relation-preview-title i {
            color: #c89b3c;
        }

        @media (max-width: 767.98px) {
            .genealogy-detail-grid,
            .genealogy-flow-grid {
                grid-template-columns: 1fr;
            }

            .genealogy-flow-relation {
                min-height: 54px;
            }
        }

        @media (max-width: 575.98px) {
            #genealogyModal .modal-dialog,
            #genealogyDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #genealogyModal .modal-content,
            #genealogyDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            #genealogyModal .modal-footer,
            #genealogyDetailModal .modal-footer {
                flex-direction: column;
                gap: 8px;
            }

            #genealogyModal .modal-footer .btn,
            #genealogyDetailModal .modal-footer .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.genealogyRoutes = {
            index: @json(route('admin.cattle-genealogy.index')),
            list: @json(route('admin.cattle-genealogy.list')),
        };

        window.genealogyCattle = @json($cattleOptions);
        window.maxGenealogyGeneration = @json($maxGenerationLevel);
    </script>
    @vite(['resources/js/pages/cattle-genealogy.js'])
@endpush
