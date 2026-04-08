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
