@extends('layouts.app')

@section('subtitle', 'Veterinarios / Certificadores')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-user-md"></i>
                </span>
                <div>
                    <h1 class="module-title">Veterinarios / Certificadores</h1>
                    <p class="module-subtitle">
                        Administra los profesionales responsables de la atención, control sanitario y certificación del ganado.
                    </p>
                </div>
            </div>

            @can('admin.veterinarians.store')
                <button class="btn btn-create" id="newVeterinarianButton" type="button" data-toggle="modal"
                    data-target="#veterinarianModal">
                    <i class="fas fa-plus"></i> Nuevo Veterinario
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Veterinarios / Certificadores</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableVeterinarian" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Documento</th>
                            <th>Nombre completo</th>
                            <th>Colegiatura</th>
                            <th>Especialidad</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.veterinarians.partials.modal')
    @include('admin.veterinarians.partials.detail-modal')
@stop

@push('css')
    <style>
        #veterinarianModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #veterinarianModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #veterinarianModal .veterinarian-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #veterinarianModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #veterinarianModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        .veterinarian-signature-card {
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

        .veterinarian-signature-card:hover {
            border-color: #1f4d36;
            box-shadow: 0 10px 25px rgba(31, 77, 54, .12);
            transform: translateY(-1px);
        }

        .veterinarian-signature-preview-wrap,
        .veterinarian-detail-signature-wrap {
            align-items: center;
            background: #fff;
            border: 1px solid #e4ece8;
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .10);
            display: inline-flex;
            flex: 0 0 auto;
            height: 110px;
            justify-content: center;
            overflow: hidden;
            padding: 10px;
            width: 240px;
        }

        .veterinarian-signature-preview,
        .veterinarian-detail-signature {
            height: 100%;
            object-fit: contain;
            width: 100%;
        }

        .veterinarian-signature-placeholder,
        .veterinarian-detail-signature-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            flex-direction: column;
            font-size: 2.35rem;
            gap: 4px;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .veterinarian-signature-placeholder span,
        .veterinarian-detail-signature-placeholder span {
            color: #6c7f73;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .veterinarian-signature-title {
            color: #1f4d36;
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .veterinarian-signature-subtitle,
        .veterinarian-signature-filename {
            color: #728277;
            font-size: .8rem;
        }

        .veterinarian-signature-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 12px 0 8px;
        }

        .btn-signature-upload {
            background: #1f4d36;
            border: 1px solid #1f4d36;
            border-radius: 10px;
            color: #fff;
            font-weight: 700;
            padding: 8px 14px;
        }

        .btn-signature-upload:hover,
        .btn-signature-upload:focus {
            background: #123524;
            border-color: #123524;
            color: #fff;
        }

        .btn-signature-remove {
            background: #fff;
            border: 1px solid #e2d4b5;
            border-radius: 10px;
            color: #795b20;
            font-weight: 700;
            padding: 8px 12px;
        }

        .veterinarian-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .veterinarian-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .veterinarian-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        @media (max-width: 575.98px) {
            #veterinarianModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #veterinarianModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .veterinarian-signature-card {
                align-items: center;
                flex-direction: column;
                text-align: center;
            }

            .veterinarian-signature-preview-wrap,
            .veterinarian-detail-signature-wrap {
                max-width: 100%;
                width: 100%;
            }

            .veterinarian-signature-actions {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.veterinarianRoutes = {
            index: @json(route('admin.veterinarians.index')),
            list: @json(route('admin.veterinarians.list')),
            consultDocument: @json(route('admin.documents.consult', ['numero' => '__NUMBER__'])),
        };
    </script>
    @vite(['resources/js/pages/veterinarian.js'])
@endpush
