@extends('layouts.app')

@section('subtitle', 'Roles')

@section('header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-user-tag"></i> Roles de Usuario

                    @can('admin.roles.store')
                        <button class="btn btn-app bg-dark" type="button" data-toggle="modal" data-target="#roleModal">
                            <i class="fas fa-plus-circle"></i> Nuevo
                        </button>
                    @endcan
                </h1>
            </div>

            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-user-tag"></i> Roles
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
                <table id="tableRole"
                    class="tableStiles table table-hover align-middle mb-0 text-center shadow-sm rounded-lg">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>ROL</th>
                            <th>GUARD</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    @include('admin.roles.partials.modal')

@stop

@push('css')
    {{-- Se heredan los estilos globales (igual que Clientes) --}}
@endpush

@push('js')
    <script>
        window.routes = {
            rolesList: "{{ route('admin.roles.list') }}",
            storeRole: "{{ route('admin.roles.store') }}",
            deleteRole: "{{ url('admin/roles') }}"
        };
    </script>

    @vite(['resources/js/pages/roles.js'])
@endpush
