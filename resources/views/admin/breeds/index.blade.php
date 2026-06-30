@extends('layouts.app')

@section('subtitle', 'Razas de Ganado')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-dna"></i>
                </span>
                <div>
                    <h1 class="module-title">Razas de Ganado</h1>
                    <p class="module-subtitle">
                        Administra las razas ganaderas usadas para clasificar, certificar y rastrear la pureza del ganado.
                    </p>
                </div>
            </div>

            @can('admin.breeds.store')
                <button class="btn btn-create" id="newBreedButton" type="button" data-toggle="modal"
                    data-target="#breedModal">
                    <i class="fas fa-plus"></i> Nueva Raza
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Razas de Ganado</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableBreed" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>País de origen</th>
                            <th>Estado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.breeds.partials.modal')
    @include('admin.breeds.partials.detail-modal')
@stop

@push('css')
    <style>
        #breedModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 900px;
            margin: 20px auto;
        }

        #breedModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #breedModal .breed-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #breedModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #breedModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        #breedModal .tox-tinymce {
            border-radius: .25rem;
            width: 100% !important;
        }

        #breedModal .tox-tinymce.is-invalid {
            border-color: #dc3545;
        }

        .breed-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .breed-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .breed-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .breed-code-chip {
            background: #fff8ea;
            border: 1px solid #e2d4b5;
            border-radius: 12px;
            color: #795b20;
            display: inline-block;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: .08em;
            padding: .35rem .7rem;
        }

        .breed-table-thumb,
        .breed-table-thumb-placeholder {
            align-items: center;
            border-radius: 12px;
            display: inline-flex;
            height: 48px;
            justify-content: center;
            overflow: hidden;
            width: 58px;
        }

        .breed-table-thumb {
            object-fit: cover;
        }

        .breed-table-thumb-placeholder {
            background: #eef6f2;
            border: 1px solid #dce8e2;
            color: #6c8a77;
            font-size: 1.2rem;
        }

        .breed-image-upload-card {
            align-items: center;
            background: #f8fafc;
            border: 1px dashed #cfd8dc;
            border-radius: 12px;
            display: flex;
            gap: 1rem;
            padding: .85rem;
        }

        .breed-image-preview {
            align-items: center;
            background: #fff;
            border: 1px solid #e4ece8;
            border-radius: 12px;
            color: #6c757d;
            display: flex;
            flex: 0 0 118px;
            flex-direction: column;
            font-size: .8rem;
            height: 86px;
            justify-content: center;
            overflow: hidden;
        }

        .breed-image-preview i {
            font-size: 1.7rem;
            margin-bottom: .25rem;
        }

        .breed-image-preview img,
        .breed-detail-image img {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .breed-detail-image,
        .breed-detail-image-placeholder {
            border-radius: 14px;
            min-height: 140px;
            overflow: hidden;
        }

        .breed-detail-image {
            background: #f8fafc;
            border: 1px solid #e4ece8;
        }

        .breed-detail-image-placeholder {
            align-items: center;
            color: #6c8a77;
            display: flex;
            flex-direction: column;
            gap: .35rem;
            justify-content: center;
        }

        .breed-detail-image-placeholder i {
            font-size: 2rem;
        }

        .breed-detail-content {
            line-height: 1.65;
        }

        .breed-detail-content p:last-child,
        .breed-detail-content ul:last-child,
        .breed-detail-content ol:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 575.98px) {
            #breedModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #breedModal .modal-content {
                max-height: calc(100vh - 16px);
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.breedRoutes = {
            index: @json(route('admin.breeds.index')),
            list: @json(route('admin.breeds.list')),
        };
    </script>
    @vite(['resources/js/pages/breed.js'])
@endpush
