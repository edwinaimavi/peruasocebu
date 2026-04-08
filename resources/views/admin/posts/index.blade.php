@extends('layouts.app')

@section('subtitle', 'Posts')

@section('header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6 d-flex align-items-center gap-3">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-newspaper text-info mr-1"></i>
                    Posts

                    {{-- @can('admin.posts.store') --}}
                    <button class="btn btn-app bg-dark btn-new" type="button" data-toggle="modal" data-target="#postModal">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </button>
                    {{-- @endcan --}}
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
                            Posts
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
                <table id="tablePost" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">ID</th>
                            <th>TÍTULO</th>
                            <th width="20%">AUTOR</th>
                            <th width="10%">ESTADO</th>
                            <th width="10%">ACCIONES</th>
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    @include('admin.posts.partials.modal')
    @include('admin.posts.partials.view-modal')

@stop

@push('css')
    <style type="text/css">
        #viewContent p {
            margin-bottom: 1rem;
        }

        #viewContent h1,
        #viewContent h2,
        #viewContent h3 {
            margin-top: 1.5rem;
            margin-bottom: .75rem;
        }
    </style>
@endpush

@push('js')
    <script>
        window.routes = {
            postList: "{{ route('admin.posts.list') }}", 
            postStore: "{{ route('admin.posts.store') }}",
            postMeta: "{{ route('admin.posts.meta', ':id') }}",
            postView: "{{ route('admin.posts.view', ':id') }}"
        }
    </script>

    @vite(['resources/js/pages/post.js'])
@endpush
