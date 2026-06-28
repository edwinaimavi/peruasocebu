@extends('layouts.app')

@section('subtitle', 'Mensajes de Contacto')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <div>
                    <h1 class="module-title">Mensajes de Contacto</h1>
                    <p class="module-subtitle">
                        Administra los mensajes enviados desde la pagina publica.
                    </p>
                </div>
            </div>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Mensajes de Contacto</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="contact-filter-bar mb-3">
                <button type="button" class="btn btn-sm btn-filter active" data-status="">
                    <i class="fas fa-layer-group mr-1"></i> Todos
                </button>
                @foreach ($statuses as $status => $label)
                    <button type="button" class="btn btn-sm btn-filter" data-status="{{ $status }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="table-responsive">
                <table id="tableContactMessage" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Telefono</th>
                            <th>Correo</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.contact_messages.partials.detail-modal')
@stop

@push('css')
    <style>
        #contactMessageDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 980px;
            margin: 20px auto;
        }

        #contactMessageDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #contactMessageDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .contact-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .btn-filter {
            background: #f5f7f6;
            border: 1px solid #dfe8e2;
            color: #315242;
            font-weight: 700;
        }

        .btn-filter.active {
            background: #1f4d36;
            border-color: #1f4d36;
            color: #fff;
        }

        #tableContactMessage tr.contact-message-new td {
            background: #f0f8f3;
            font-weight: 700;
        }

        .contact-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #edf6f0);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .contact-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .contact-detail-value {
            color: #2f3b43;
            min-height: 24px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .contact-message-box {
            background: #fff;
            border: 1px solid #e6ece8;
            border-radius: 12px;
            line-height: 1.7;
            padding: 18px;
        }

        @media (max-width: 575.98px) {
            #contactMessageDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #contactMessageDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.contactMessageRoutes = {
            index: @json(route('admin.contact-messages.index')),
            list: @json(route('admin.contact-messages.list')),
        };
    </script>
    @vite(['resources/js/pages/contact-message.js'])
@endpush
