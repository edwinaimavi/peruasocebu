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

        .owner-photo-card {
            align-items: center;
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(31, 77, 54, .07);
            display: flex;
            gap: 18px;
            padding: 18px;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .owner-photo-card:hover {
            border-color: #1f4d36;
            box-shadow: 0 10px 25px rgba(31, 77, 54, .12);
            transform: translateY(-1px);
        }

        .owner-photo-preview-wrap,
        .owner-detail-photo-wrap {
            align-items: center;
            background: #fff;
            border: 4px solid #fff;
            border-radius: 50%;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
            display: inline-flex;
            flex: 0 0 auto;
            height: 120px;
            justify-content: center;
            overflow: hidden;
            width: 120px;
        }

        .owner-photo-preview,
        .owner-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .owner-photo-placeholder,
        .owner-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            flex-direction: column;
            font-size: 2.55rem;
            gap: 4px;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .owner-photo-placeholder span {
            color: #6c7f73;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .owner-photo-controls {
            flex: 1 1 auto;
            min-width: 0;
        }

        .owner-photo-title {
            color: #1f4d36;
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .owner-photo-subtitle,
        .owner-photo-filename {
            color: #728277;
            font-size: .8rem;
        }

        .owner-photo-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 12px 0 8px;
        }

        .btn-photo-upload {
            background: #1f4d36;
            border: 1px solid #1f4d36;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            padding: 8px 14px;
        }

        .btn-photo-upload:hover,
        .btn-photo-upload:focus {
            background: #123524;
            border-color: #123524;
            color: #fff;
        }

        .btn-photo-remove {
            background: #fff;
            border: 1px solid #e2d4b5;
            border-radius: 10px;
            color: #795b20;
            font-weight: 700;
            padding: 8px 12px;
        }

        .btn-photo-remove:hover,
        .btn-photo-remove:focus {
            background: #fff8ea;
            color: #5f4311;
        }

        @media (max-width: 575.98px) {
            #ownerModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #ownerModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .owner-photo-card {
                align-items: flex-start;
                flex-direction: column;
                text-align: center;
            }

            .owner-photo-controls {
                width: 100%;
            }

            .owner-photo-actions {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.ownerRoutes = {
            index: @json(route('admin.owners.index')),
            list: @json(route('admin.owners.list')),
            consultDocument: @json(route('admin.documents.consult', ['numero' => '__NUMBER__'])),
        };
    </script>
    @vite(['resources/js/pages/owner.js'])
@endpush
