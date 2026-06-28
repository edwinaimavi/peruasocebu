@extends('layouts.app')

@section('subtitle', 'Certificados')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-certificate"></i>
                </span>
                <div>
                    <h1 class="module-title">Certificados</h1>
                    <p class="module-subtitle">
                        Administra certificados de raza, genealogia, propiedad y pureza del ganado.
                    </p>
                </div>
            </div>

            @can('admin.certificates.store')
                <button class="btn btn-create" id="newCertificateButton" type="button" data-toggle="modal"
                    data-target="#certificateModal">
                    <i class="fas fa-plus"></i> Nuevo Certificado
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Certificados</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableCertificate" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nro. Certificado</th>
                            <th>Ganado</th>
                            <th>Codigo</th>
                            <th>Tipo</th>
                            <th>Propietario</th>
                            <th>Criadero</th>
                            <th>Pureza</th>
                            <th>Fecha emision</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.certificates.partials.modal')
    @include('admin.certificates.partials.detail-modal')
@stop

@push('css')
    <style>
        #certificateModal .modal-dialog,
        #certificateDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1040px;
            margin: 20px auto;
        }

        #certificateModal .modal-content,
        #certificateDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #certificateModal .certificate-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #certificateModal .modal-body,
        #certificateDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .certificate-section-title {
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

        .certificate-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #edf6f0);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .certificate-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .certificate-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .certificate-detail-item-wide {
            grid-column: 1 / -1;
        }

        .certificate-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .certificate-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .certificate-qr {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            height: 132px;
            object-fit: contain;
            padding: 8px;
            width: 132px;
        }

        .certificate-photo {
            border-radius: 10px;
            height: 96px;
            object-fit: cover;
            width: 96px;
        }

        .certificate-signature-thumb {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 6px;
            height: 54px;
            max-width: 92px;
            object-fit: contain;
            padding: 4px;
        }

        @media (max-width: 575.98px) {
            #certificateModal .modal-dialog,
            #certificateDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #certificateModal .modal-content,
            #certificateDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .certificate-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.certificateRoutes = {
            index: @json(route('admin.certificates.index')),
            list: @json(route('admin.certificates.list')),
            cattleInfo: @json(url('admin/certificates/cattle-info')),
            pdfBase: @json(url('admin/certificates')),
            signatures: @json(route('admin.certificate-signatures.index')),
        };
    </script>
    @vite(['resources/js/pages/certificate.js'])
@endpush
