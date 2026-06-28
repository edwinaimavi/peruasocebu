@extends('layouts.app')

@section('subtitle', 'Firmas de Certificados')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-signature"></i>
                </span>
                <div>
                    <h1 class="module-title">Firmas de Certificados</h1>
                    <p class="module-subtitle">
                        Administra las firmas y sellos utilizados en los certificados emitidos.
                    </p>
                </div>
            </div>

            @can('admin.certificate-signatures.store')
                <button class="btn btn-create" id="newCertificateSignatureButton" type="button" data-toggle="modal"
                    data-target="#certificateSignatureModal">
                    <i class="fas fa-plus"></i> Nueva Firma
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Firmas de Certificados</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableCertificateSignature" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Certificado</th>
                            <th>Tipo persona</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Firma</th>
                            <th>Sello</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.certificate_signatures.partials.modal')
    @include('admin.certificate_signatures.partials.detail-modal')
@stop

@push('css')
    <style>
        #certificateSignatureModal .modal-dialog,
        #certificateSignatureDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #certificateSignatureModal .modal-content,
        #certificateSignatureDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #certificateSignatureModal .certificate-signature-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #certificateSignatureModal .modal-body,
        #certificateSignatureDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .signature-section-title {
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

        .signature-upload-card {
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 12px;
            min-height: 180px;
            padding: 16px;
        }

        .signature-preview {
            align-items: center;
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            display: flex;
            height: 110px;
            justify-content: center;
            margin-top: 12px;
            overflow: hidden;
        }

        .signature-preview img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .signature-detail-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .signature-detail-item {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            padding: 12px;
        }

        .signature-detail-item-wide {
            grid-column: 1 / -1;
        }

        .signature-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .signature-detail-value {
            color: #2f3b43;
            min-height: 24px;
            word-break: break-word;
        }

        .signature-detail-image {
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 8px;
            max-height: 150px;
            max-width: 100%;
            object-fit: contain;
            padding: 8px;
        }

        @media (max-width: 575.98px) {
            #certificateSignatureModal .modal-dialog,
            #certificateSignatureDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #certificateSignatureModal .modal-content,
            #certificateSignatureDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .signature-detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.certificateSignatureRoutes = {
            index: @json(route('admin.certificate-signatures.index')),
            list: @json(route('admin.certificate-signatures.list')),
            certificates: @json(route('admin.certificates.index')),
        };
    </script>
    @vite(['resources/js/pages/certificate-signature.js'])
@endpush
