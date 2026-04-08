@extends('layouts.app')

@section('subtitle', 'Pages')

@section('header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6 d-flex align-items-center gap-3">
                <h1 class="m-0 text-bold text-dark">
                    <i class="fas fa-file-alt text-info mr-1"></i>
                    Pages

                    <button class="btn btn-app bg-dark btn-new" type="button" data-toggle="modal" data-target="#pageModal">
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
                            Pages
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
                <table id="tablePage" class="table table-hover table-bordered align-middle mb-0 text-center">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th width="5%">#</th>
                            <th width="5%">ID</th>
                            <th>TÍTULO</th>
                            <th width="25%">SLUG</th>
                            <th width="10%">ESTADO</th>
                           
                        </tr>
                    </thead>
                </table>
            </div>

        </div>
    </div>

    {{-- Modal --}}
    @include('admin.pages.partials.modal')
    @include('admin.pages.partials.view-modal')


@stop

@push('css')
    <style>
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
            pageList: "{{ route('admin.pages.list') }}", // DataTable
            pageView: "{{ route('admin.pages.view', ':id') }}",
        }
    </script>

    @vite(['resources/js/pages/page.js'])
@endpush
