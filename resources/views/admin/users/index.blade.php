@extends('layouts.app')

@section('subtitle', 'Usuarios')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-users"></i>
                </span>
                <div>
                    <h1 class="module-title">Usuarios</h1>
                    <p class="module-subtitle">
                        Administra los accesos, datos de contacto y roles del equipo de PERU ASOCEBU.
                    </p>
                </div>
            </div>

            @can('admin.users.store')
                <button class="btn btn-create" type="button" data-toggle="modal" data-target="#userModal">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Usuarios</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableUser" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Celular</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.users.partials.modal')
@stop

@push('css')
    {{-- estilos heredados desde layout --}}
@endpush

@push('js')
    <script>
        window.routes = {
            storeUser: "{{ route('admin.users.store') }}",
            usersList: "{{ route('admin.users.list') }}",
            deleteUser: "{{ url('admin/users') }}"
        };

        function previewImage(event, querySelector) {
            let input = event.target;
            let imgPreview = document.querySelector(querySelector);
            if (!input.files.length) return;

            let file = input.files[0];
            let objectURL = URL.createObjectURL(file);
            imgPreview.src = objectURL;
        }
    </script>

    @vite(['resources/js/pages/user.js'])
@endpush
