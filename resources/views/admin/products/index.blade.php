@extends('layouts.app')

@section('subtitle', 'Products')

@section('header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6 d-flex align-items-center gap-3">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-boxes text-info mr-1"></i>
                    Products

                    <button class="btn btn-app bg-dark btn-new" type="button" data-toggle="modal" data-target="#productModal">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </button>
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
                            Products
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
                <table id="tableProduct" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">ID</th>
                            <th>NOMBRE</th>
                            <th width="15%">TIPO</th>
                            <th width="15%">PRECIO</th>
                            <th width="15%">ESTADO</th>
                            <th width="10%">ACCIONES</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    {{-- Modales --}}
    @include('admin.products.partials.modal')
    @include('admin.products.partials.view-modal')

@stop

@push('css')
    <style>
        .badge-sistema {
            background-color: #17a2b8;
        }

        .badge-servicio {
            background-color: #6f42c1;
        }



        /* CARD */
        .gallery-card {
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        /* HEADER */
        .gallery-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .gallery-header span {
            font-weight: 600;
            color: #333;
        }

        .gallery-header small {
            color: #999;
        }

        /* UPLOAD BOX */
        .upload-box {
            border: 2px dashed #dce3ea;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all .2s ease;
            margin-bottom: 10px;
        }

        .upload-box:hover {
            border-color: #007bff;
            background: #f8fbff;
        }

        .upload-content i {
            font-size: 22px;
            color: #007bff;
            display: block;
            margin-bottom: 5px;
        }

        /* GRID */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 10px;
        }

        /* ITEM */
        .gallery-item {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
        }

        .gallery-item img {
            width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* HOVER DELETE */
        .gallery-item .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.85);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 4px 6px;
            font-size: 12px;
            opacity: 0;
            transition: .2s;
        }

        .gallery-item:hover .delete-btn {
            opacity: 1;
        }

        /* EMPTY */
        .empty-gallery {
            text-align: center;
            color: #aaa;
            margin-top: 15px;
        }

        .empty-gallery i {
            font-size: 30px;
            margin-bottom: 5px;
        }
    </style>
@endpush

@push('js')
    <script>
        window.routes = {
            productList: "{{ route('admin.products.list') }}",
            productView: "{{ route('admin.products.view', ':id') }}"
        }


    </script>

    @vite(['resources/js/pages/product.js'])
@endpush
