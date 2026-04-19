@extends('layouts.app')

@section('subtitle', 'Usuarios')

@section('header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6 d-flex align-items-center gap-3">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-layer-group text-info mr-1"></i>
                    Categorías


                    {{-- @can('admin.users.store') --}}
                    <button class="btn btn-app bg-dark btn-new" type="button" data-toggle="modal" data-target="#categoryModal">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </button>
                    {{--      @endcan --}}
                </h1>
            </div>

            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="text-info">
                                <i class="fa fa-house-user"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Categorías
                        </li>
                    </ol>
                </nav>
            </div>

        </div>
    </div>
@stop


@section('content_body')

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-3">

            <div class="table-responsive">
                <table id="tableCategory" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">ID</th>
                            <th>NOMBRE</th>
                            <th>DESCRIPCIÓN</th>
                            <th width="10%">ESTADO</th>
                            <th width="10%">ACCIONES</th>
                        </tr>
                    </thead>
                </table>

            </div>

        </div>
    </div>

    {{-- Modal --}}
    @include('admin.categories.partials.modal')

@stop

@push('css')
    <style>
        /* CONTENEDOR */
        .upload-box-modern {
            border: 2px dashed #d9e2ec;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
        }

        .upload-box-modern:hover {
            border-color: #5a67d8;
            background: #f7f9ff;
        }

        /* PLACEHOLDER */
        .upload-placeholder i {
            font-size: 30px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .upload-placeholder p {
            margin: 0;
            font-weight: 500;
        }

        .upload-placeholder small {
            color: #94a3b8;
        }

        /* PREVIEW */
        #imagePreview {
            position: relative;
        }

        #imagePreview img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* BOTÓN ELIMINAR */
        .btn-remove {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            cursor: pointer;




        }



        .cropper-view-box {
            border-radius: 10px;
            outline: none;
        }

        .cropper-face {
            background-color: rgba(0, 0, 0, 0.2);
        }


        .upload-box-modern.disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: not-allowed;
        }

        .upload-box-modern.no-upload {
            cursor: not-allowed;
        }

        .upload-box-modern.no-upload #uploadPlaceholder {
            pointer-events: none;
            opacity: 0.4;
        }
    </style>
@endpush



@push('js')
    <script>
        window.routes = {
            categoryList: "{{ route('admin.categories.list') }}", // GET lista para DataTables
        }
    </script>
    @vite(['resources/js/pages/category.js'])
@endpush
