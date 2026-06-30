@extends('layouts.app')

@section('subtitle', 'Ganado')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-paw"></i>
                </span>
                <div>
                    <h1 class="module-title">Ganado</h1>
                    <p class="module-subtitle">
                        Administra el registro principal del ganado, su raza, propietario, criadero y datos genealógicos.
                    </p>
                </div>
            </div>

            @can('admin.cattle.store')
                <button class="btn btn-create" id="newCattleButton" type="button" data-toggle="modal"
                    data-target="#cattleModal">
                    <i class="fas fa-plus"></i> Nuevo Ganado
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Ganado</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableCattle" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Raza</th>
                            <th>Sexo</th>
                            <th>Criadero</th>
                            <th>Propietario</th>
                            <th>Pureza</th>
                            <th>Estado</th>
                            <th>Venta</th>
                            <th>Público</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.cattle.partials.modal')
    @include('admin.cattle.partials.detail-modal')
@stop

@push('css')
    <style>
        #cattleModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1080px;
            margin: 20px auto;
        }

        #cattleModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #cattleModal .cattle-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #cattleModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #cattleModal .modal-footer {
            flex: 0 0 auto;
            background: #fff;
            border-top: 1px solid #e6eaee;
        }

        .cattle-section-title {
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

        .cattle-photo-card {
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(31, 77, 54, .07);
            padding: 18px;
        }

        .cattle-photo-main,
        .cattle-gallery-upload {
            align-items: center;
            display: flex;
            gap: 18px;
        }

        .cattle-photo-preview-wrap,
        .cattle-detail-photo-wrap {
            align-items: center;
            background: #fff;
            border: 4px solid #fff;
            border-radius: 18px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .12);
            display: inline-flex;
            flex: 0 0 auto;
            height: 140px;
            justify-content: center;
            overflow: hidden;
            width: 140px;
        }

        .cattle-photo-preview,
        .cattle-detail-photo {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .cattle-photo-placeholder,
        .cattle-detail-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: flex;
            flex-direction: column;
            font-size: 2.7rem;
            gap: 5px;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .cattle-photo-placeholder span {
            color: #6c7f73;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .cattle-photo-controls {
            flex: 1 1 auto;
            min-width: 0;
        }

        .cattle-photo-title {
            color: #1f4d36;
            font-size: .95rem;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .cattle-photo-subtitle,
        .cattle-photo-filename {
            color: #728277;
            font-size: .8rem;
        }

        .cattle-photo-actions {
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

        .cattle-gallery-preview,
        .cattle-photo-gallery-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            margin-top: 14px;
        }

        .cattle-gallery-selection-summary {
            align-items: center;
            background: #eef6f2;
            border: 1px solid #d8e7de;
            border-radius: 12px;
            color: #1f4d36;
            display: flex;
            font-size: .82rem;
            font-weight: 800;
            justify-content: center;
            min-height: 44px;
            padding: 10px;
        }

        .cattle-gallery-preview-item,
        .cattle-gallery-item {
            background: #fff;
            border: 1px solid #e4ece8;
            border-radius: 12px;
            box-shadow: 0 8px 18px rgba(31, 77, 54, .06);
            overflow: hidden;
            position: relative;
        }

        .cattle-gallery-preview-item img,
        .cattle-gallery-item img {
            aspect-ratio: 4 / 3;
            display: block;
            object-fit: cover;
            width: 100%;
        }

        .cattle-gallery-item-body {
            padding: 10px;
        }

        .cattle-gallery-preview-name {
            line-height: 1.25;
            word-break: break-word;
        }

        .gallery-remove-btn {
            align-items: center;
            background: #dc3545;
            border: 0;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-size: .75rem;
            height: 26px;
            justify-content: center;
            position: absolute;
            right: 7px;
            top: 7px;
            width: 26px;
            z-index: 2;
        }

        .gallery-remove-btn:hover,
        .gallery-remove-btn:focus {
            background: #bd2130;
            color: #fff;
            outline: 0;
        }

        .cattle-gallery-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .cattle-table-photo {
            border: 2px solid #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .12);
            display: inline-flex;
            height: 42px;
            object-fit: cover;
            width: 42px;
        }

        .cattle-table-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            justify-content: center;
        }

        .cattle-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            margin-bottom: .2rem;
            text-transform: uppercase;
        }

        .cattle-detail-value {
            color: #2f3b43;
            min-height: 24px;
            overflow-wrap: break-word;
            white-space: normal;
            word-break: break-word;
        }

        .cattle-detail-hero,
        .cattle-genealogy-card {
            background: linear-gradient(135deg, #f8fafc, #eef6f2);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .cattle-code-chip {
            background: #fff8ea;
            border: 1px solid #e2d4b5;
            border-radius: 12px;
            color: #795b20;
            display: inline-block;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: .06em;
            max-width: 100%;
            overflow-wrap: break-word;
            padding: .35rem .7rem;
            white-space: normal;
            word-break: break-word;
        }

        .min-w-0 {
            min-width: 0;
        }

        #cattleDetailModal * {
            box-sizing: border-box;
        }

        #cattleDetailModal .modal-dialog {
            max-width: 980px;
            width: calc(100% - 24px);
            margin: 12px auto;
        }

        #cattleDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 24px);
            overflow: hidden;
            border-radius: 18px;
        }

        #cattleDetailModal .modal-header,
        #cattleDetailModal .modal-footer {
            flex: 0 0 auto;
        }

        #cattleDetailModal .modal-body {
            flex: 1 1 auto;
            max-height: calc(100vh - 180px);
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        #cattleDetailModal .card,
        #cattleDetailModal .card-body,
        #cattleDetailModal .cattle-detail-card,
        #cattleDetailModal .cattle-genealogy-card,
        #cattleDetailModal .cattle-genealogy-parent-card {
            max-width: 100%;
            min-width: 0;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        #cattleDetailModal .cattle-detail-hero {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            min-width: 0;
            overflow: hidden;
        }

        #cattleDetailModal .cattle-detail-photo-wrap {
            flex: 0 0 auto;
            height: 120px;
            width: 160px;
        }

        #cattleDetailModal .cattle-detail-info {
            flex: 1 1 260px;
            min-width: 0;
        }

        #cattleDetailModal .cattle-detail-name {
            line-height: 1.25;
            max-width: 100%;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        #cattleDetailModal .cattle-detail-badges {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-width: 0;
        }

        #cattleDetailModal .cattle-detail-status-badges {
            flex: 0 1 190px;
            justify-content: flex-end;
        }

        #cattleDetailModal .badge {
            max-width: 100%;
            white-space: normal;
            word-break: break-word;
        }

        .cattle-detail-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .cattle-detail-item {
            min-width: 0;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .cattle-detail-item-wide {
            grid-column: 1 / -1;
        }

        .badge-orange {
            background-color: #fd7e14;
            color: #fff;
        }

        .cattle-genealogy-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .cattle-detail-tabs-wrapper {
            background: #fff;
            padding-top: 4px;
            position: sticky;
            top: 0;
            z-index: 5;
        }

        .cattle-detail-tabs {
            border-bottom: 1px solid rgba(18, 53, 36, .10);
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .cattle-detail-tabs .nav-link {
            background: #f8fbf8;
            border: 1px solid rgba(18, 53, 36, .10);
            border-radius: 999px;
            color: #123524;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 8px;
            padding: 8px 14px;
        }

        .cattle-detail-tabs .nav-link i {
            color: #c89b3c;
            margin-right: 5px;
        }

        .cattle-detail-tabs .nav-link.active {
            background: #123524;
            border-color: #123524;
            color: #fff;
        }

        .cattle-detail-tabs .nav-link.active i {
            color: #d8be78;
        }

        .cattle-detail-tab-content {
            padding-top: 18px;
        }

        .cattle-detail-tab-content .tab-pane > .cattle-detail-card,
        .cattle-detail-tab-content .tab-pane > .cattle-genealogy-card,
        .cattle-detail-tab-content .tab-pane > .cattle-gallery-card {
            margin-top: 0 !important;
        }

        .cattle-detail-tab-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        #cattleDetailModal .cattle-genealogy-grid {
            display: none;
        }

        .cattle-genealogy-tree {
            max-width: 100%;
        }

        .pedigree-tree {
            background: #f8fbf8;
            border: 1px solid rgba(18, 53, 36, .08);
            border-radius: 16px;
            overflow-x: auto;
            padding: 18px;
            position: relative;
        }

        .pedigree-lines {
            height: 100%;
            inset: 0;
            overflow: visible;
            pointer-events: none;
            position: absolute;
            width: 100%;
            z-index: 1;
        }

        .pedigree-line-path {
            fill: none;
            stroke: rgba(18, 53, 36, .32);
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
        }

        .pedigree-line-path.strong {
            stroke: rgba(200, 155, 60, .55);
        }

        .pedigree-branches {
            align-items: end;
            display: grid;
            gap: 24px;
            grid-template-columns: 1fr 1fr;
            min-width: 620px;
        }

        .pedigree-branch {
            position: relative;
            text-align: center;
        }

        .pedigree-branch-title,
        .pedigree-extended-title {
            align-items: center;
            background: #123524;
            border-radius: 999px;
            color: #fff;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            justify-content: center;
            margin-bottom: 12px;
            padding: 5px 12px;
        }

        .pedigree-ancestor-row {
            align-items: flex-end;
            display: flex;
            gap: 10px;
            justify-content: center;
            min-height: 108px;
        }

        .pedigree-parent-row,
        .pedigree-current-row {
            display: flex;
            justify-content: center;
        }

        .pedigree-line-down {
            background: rgba(18, 53, 36, .25);
            height: 18px;
            margin: 8px auto;
            width: 2px;
        }

        .pedigree-join-line {
            border-bottom: 2px solid rgba(18, 53, 36, .25);
            border-left: 2px solid rgba(18, 53, 36, .25);
            border-radius: 0 0 12px 12px;
            border-right: 2px solid rgba(18, 53, 36, .25);
            height: 22px;
            margin: 12px auto 10px;
            width: 48%;
        }

        .pedigree-node {
            background: #fff;
            border: 1px solid rgba(18, 53, 36, .10);
            border-radius: 14px;
            box-shadow: 0 8px 22px rgba(18, 53, 36, .07);
            padding: 10px;
            position: relative;
            text-align: center;
            width: 170px;
            z-index: 2;
        }

        .pedigree-node.compact {
            padding: 9px;
            width: 145px;
        }

        .pedigree-node-empty {
            background: #fbfaf6;
            border-style: dashed;
            opacity: .55;
        }

        .pedigree-node-avatar {
            align-items: center;
            background: #eef5ef;
            border: 2px solid rgba(200, 155, 60, .38);
            border-radius: 50%;
            color: #123524;
            display: flex;
            font-size: 18px;
            height: 48px;
            justify-content: center;
            margin: 0 auto 7px;
            overflow: hidden;
            width: 48px;
        }

        .pedigree-node.compact .pedigree-node-avatar {
            font-size: 16px;
            height: 42px;
            width: 42px;
        }

        .pedigree-node-avatar img {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .pedigree-node-info small,
        .pedigree-node small {
            color: #c89b3c;
            display: block;
            font-size: 9px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .pedigree-node-info strong,
        .pedigree-node strong {
            color: #123524;
            display: block;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 3px;
        }

        .pedigree-node-info span {
            color: #64748b;
            display: block;
            font-size: 10px;
            line-height: 1.25;
        }

        .current-animal {
            background: linear-gradient(180deg, #fff 0%, #fffaf0 100%);
            border-color: rgba(200, 155, 60, .65);
        }

        .pedigree-extended {
            margin-top: 18px;
            text-align: center;
        }

        .pedigree-extended-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .genealogy-empty {
            color: #64748b;
            padding: 30px;
            text-align: center;
        }

        .genealogy-empty i {
            color: #c89b3c;
            display: block;
            font-size: 34px;
            margin-bottom: 10px;
        }

        @media (max-width: 991.98px) {
            .cattle-detail-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 575.98px) {
            #cattleModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #cattleModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .cattle-photo-card {
                align-items: flex-start;
                text-align: center;
            }

            .cattle-photo-main,
            .cattle-gallery-upload {
                align-items: stretch;
                flex-direction: column;
            }

            .cattle-photo-controls {
                width: 100%;
            }

            .cattle-photo-actions {
                justify-content: center;
            }

            #cattleDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #cattleDetailModal .modal-content {
                max-height: calc(100vh - 16px);
                border-radius: 14px;
            }

            #cattleDetailModal .modal-header {
                align-items: flex-start;
                gap: 8px;
            }

            #cattleDetailModal .modal-body {
                max-height: calc(100vh - 136px);
                padding: 12px !important;
            }

            #cattleDetailModal .cattle-detail-hero {
                align-items: center;
                flex-direction: column;
                gap: 14px;
                text-align: center;
            }

            #cattleDetailModal .cattle-detail-photo-wrap {
                height: 150px;
                max-width: 260px;
                width: 100%;
            }

            #cattleDetailModal .cattle-detail-info,
            #cattleDetailModal .cattle-detail-status-badges {
                flex-basis: auto;
                width: 100%;
            }

            #cattleDetailModal .cattle-detail-badges,
            #cattleDetailModal .cattle-detail-status-badges {
                justify-content: center;
            }

            .cattle-detail-grid,
            .cattle-genealogy-grid {
                grid-template-columns: 1fr;
            }

            .pedigree-tree {
                padding: 14px;
            }

            .pedigree-branches {
                gap: 16px;
                min-width: 560px;
            }

            .pedigree-node {
                width: 150px;
            }

            .pedigree-node.compact {
                width: 130px;
            }

            #cattleDetailModal .modal-footer {
                flex-direction: column;
                gap: 8px;
            }

            #cattleDetailModal .modal-footer .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.cattleRoutes = {
            index: @json(route('admin.cattle.index')),
            list: @json(route('admin.cattle.list')),
            genealogy: @json(route('admin.cattle-genealogy.index')),
            ownershipHistories: @json(route('admin.ownership-histories.index')),
            sales: @json(route('admin.cattle-sales.index')),
            certificates: @json(route('admin.certificates.index')),
            veterinaryRecords: @json(route('admin.veterinary-records.index')),
            vaccinations: @json(route('admin.vaccinations.index')),
            treatments: @json(route('admin.treatments.index')),
            weightRecords: @json(route('admin.weight-records.index')),
            reproductionRecords: @json(route('admin.reproduction-records.index')),
            photosBase: @json(url('admin/cattle')),
            photoBase: @json(url('admin/cattle-photos')),
        };

        window.cattleBreeds = @json($cattleBreedOptions);
    </script>
    @vite(['resources/js/pages/cattle.js'])
@endpush
