@extends('layouts.app')

@section('subtitle', 'Vacunas del Ganado')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-syringe"></i>
                </span>
                <div>
                    <h1 class="module-title">Vacunas del Ganado</h1>
                    <p class="module-subtitle">
                        Administra el control de vacunas, dosis, lotes y proximas aplicaciones del ganado.
                    </p>
                </div>
            </div>

            @can('admin.vaccinations.store')
                <button class="btn btn-create" id="newVaccinationButton" type="button" data-toggle="modal"
                    data-target="#vaccinationModal">
                    <i class="fas fa-plus"></i> Nueva Vacuna
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Vacunas del Ganado</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableVaccination" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Vacuna</th>
                            <th>Dosis</th>
                            <th>Lote</th>
                            <th>Fecha aplicada</th>
                            <th>Proxima dosis</th>
                            <th>Veterinario</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.vaccinations.partials.modal')
    @include('admin.vaccinations.partials.detail-modal')
@stop

@push('css')
    <style>
        #vaccinationModal .modal-dialog,
        #vaccinationDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #vaccinationModal .modal-content,
        #vaccinationDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #vaccinationModal .vaccination-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #vaccinationModal .modal-body,
        #vaccinationDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .vaccination-section-title {
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

        .vaccination-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .vaccination-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .vaccination-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .vaccination-detail-photo-wrap {
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

        .vaccination-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .vaccination-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            font-size: 2.4rem;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .vaccination-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .vaccination-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .vaccination-detail-item-wide {
            grid-column: 1 / -1;
        }

        @media (max-width: 575.98px) {
            #vaccinationModal .modal-dialog,
            #vaccinationDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            .vaccination-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.vaccinationRoutes = {
            index: @json(route('admin.vaccinations.index')),
            list: @json(route('admin.vaccinations.list')),
        };
    </script>
    @vite(['resources/js/pages/vaccination.js'])
@endpush
