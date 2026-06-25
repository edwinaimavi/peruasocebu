@extends('layouts.app')

@section('subtitle', 'Propietarios / Dueños')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-user-tie"></i>
                </span>
                <div>
                    <h1 class="module-title">Propietarios / Dueños</h1>
                    <p class="module-subtitle">
                        Administra dueños actuales, anteriores, compradores y vendedores del ganado.
                    </p>
                </div>
            </div>

            @can('admin.owners.store')
                <button class="btn btn-create" id="newOwnerButton" type="button" data-toggle="modal"
                    data-target="#ownerModal">
                    <i class="fas fa-plus"></i> Nuevo Propietario
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Propietarios / Dueños</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableOwner" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                            <th>Nombre / Razón social</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.owners.partials.modal')
    @include('admin.owners.partials.detail-modal')
@stop

@push('css')
    <style>
        #ownerModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #ownerModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #ownerModal .owner-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #ownerModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #ownerModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        .owner-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .owner-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .owner-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        @media (max-width: 575.98px) {
            #ownerModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #ownerModal .modal-content {
                max-height: calc(100vh - 16px);
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.ownerRoutes = {
            index: @json(route('admin.owners.index')),
            list: @json(route('admin.owners.list')),
        };
    </script>
    @vite(['resources/js/pages/owner.js'])
@endpush
