@extends('layouts.app')

@section('subtitle', 'Usuarios')

@section('header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-users"></i> Usuarios

                    @can('admin.users.store')
                        <button class="btn btn-app bg-dark" type="button" data-toggle="modal" data-target="#userModal">
                            <i class="fas fa-plus-circle"></i> Nuevo
                        </button>
                    @endcan
                </h1>
            </div>

            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <i class="fa fa-fw fa-house-user"></i> Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-users"></i> Usuarios
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
                <table id="tableUser"
                    class="tableStiles table table-hover align-middle mb-0 text-center shadow-sm rounded-lg">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>DNI</th>
                            <th>NOMBRE</th>
                            <th>EMAIL</th>
                            <th>CEL</th>
                            <th>STATUS</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
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
