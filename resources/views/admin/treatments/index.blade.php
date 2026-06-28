@extends('layouts.app')

@section('subtitle', 'Tratamientos Medicos')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-pills"></i>
                </span>
                <div>
                    <h1 class="module-title">Tratamientos Medicos</h1>
                    <p class="module-subtitle">
                        Administra tratamientos, medicamentos, dosis, duracion y motivos clinicos del ganado.
                    </p>
                </div>
            </div>

            @can('admin.treatments.store')
                <button class="btn btn-create" id="newTreatmentButton" type="button" data-toggle="modal"
                    data-target="#treatmentModal">
                    <i class="fas fa-plus"></i> Nuevo Tratamiento
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Tratamientos Medicos</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableTreatment" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Tratamiento</th>
                            <th>Medicamento</th>
                            <th>Dosis</th>
                            <th>Duracion</th>
                            <th>Fecha</th>
                            <th>Veterinario</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.treatments.partials.modal')
    @include('admin.treatments.partials.detail-modal')
@stop

@push('css')
    <style>
        #treatmentModal .modal-dialog,
        #treatmentDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #treatmentModal .modal-content,
        #treatmentDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #treatmentModal .treatment-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #treatmentModal .modal-body,
        #treatmentDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .treatment-section-title {
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

        .treatment-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .treatment-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .treatment-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .treatment-detail-photo-wrap {
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

        .treatment-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .treatment-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .treatment-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .treatment-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .treatment-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #treatmentModal .modal-dialog,
            #treatmentDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            .treatment-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.treatmentRoutes = {
            index: @json(route('admin.treatments.index')),
            list: @json(route('admin.treatments.list')),
        };
    </script>
    @vite(['resources/js/pages/treatment.js'])
@endpush
