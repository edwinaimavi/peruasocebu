@extends('layouts.app')

@section('subtitle', 'Clientes')

@section('header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6 d-flex align-items-center gap-3">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-users text-primary mr-1"></i>
                    Clientes

                    <button class="btn btn-app bg-dark btn-new" type="button" data-toggle="modal" data-target="#customerModal">
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
                            Clientes
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
                <table id="tableCustomer" class="table table-hover table-bordered align-middle mb-0 text-center">

                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th width="5%">#</th>
                            <th width="8%">ID</th>
                            <th>NOMBRE COMPLETO</th>
                            <th width="12%">DOCUMENTO</th>
                            <th width="12%">N° DOC</th>
                            <th width="12%">TELÉFONO</th>
                            <th width="18%">EMAIL</th>
                            <th width="10%">ESTADO</th>
                            <th width="13%">ACCIONES</th>
                        </tr>
                    </thead>

                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    @include('admin.customers.partials.modal')

@stop


@push('js')
    <script>
        window.routes = {
            customerList: "{{ route('admin.customers.list') }}",
            consultarDocumento: "{{ route('admin.customers.consultar', 'DOC_PLACEHOLDER') }}"
        }
    </script>

    @vite(['resources/js/pages/customer.js'])
@endpush
