@extends('layouts.app')

@section('subtitle', 'Blog / Noticias')

@section('header')
    <div class="container-fluid">
        <div class="module-header">
            <div class="module-heading">
                <span class="module-heading-icon">
                    <i class="fas fa-newspaper"></i>
                </span>
                <div>
                    <h1 class="module-title">Blog / Noticias</h1>
                    <p class="module-subtitle">
                        Administra las noticias y publicaciones que se muestran en la pagina publica.
                    </p>
                </div>
            </div>

            @can('admin.blog-posts.store')
                <button class="btn btn-create" id="newBlogPostButton" type="button" data-toggle="modal"
                    data-target="#blogPostModal">
                    <i class="fas fa-plus"></i> Nueva Publicacion
                </button>
            @endcan
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}"><i class="fas fa-home mr-1"></i> Inicio</a>
                </li>
                <li class="breadcrumb-item active">Blog / Noticias</li>
            </ol>
        </nav>
    </div>
@stop

@section('content_body')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tableBlogPost" class="tableStiles table table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Titulo</th>
                            <th>Slug</th>
                            <th>Autor</th>
                            <th>Estado</th>
                            <th>Publicado</th>
                            <th>F. Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('admin.blog_posts.partials.modal')
    @include('admin.blog_posts.partials.detail-modal')
@stop

@push('css')
    <style>
        #blogPostModal .modal-dialog,
        #blogPostDetailModal .modal-dialog {
            width: calc(100% - 30px);
            max-width: 1080px;
            margin: 20px auto;
        }

        #blogPostModal .modal-content,
        #blogPostDetailModal .modal-content {
            display: flex;
            max-height: calc(100vh - 40px);
            overflow: hidden;
        }

        #blogPostModal .blog-post-modal-form {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }

        #blogPostModal .modal-body,
        #blogPostDetailModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .blog-section-title {
            align-items: center;
            color: #1f4d36;
            display: flex;
            font-size: .9rem;
            font-weight: 800;
            gap: 8px;
            letter-spacing: .02em;
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .blog-table-photo,
        .blog-table-photo-placeholder {
            border-radius: 8px;
            height: 48px;
            object-fit: cover;
            width: 64px;
        }

        .blog-table-photo-placeholder {
            align-items: center;
            background: #edf3ee;
            color: #1f4d36;
            display: inline-flex;
            justify-content: center;
        }

        .blog-upload-card {
            align-items: center;
            background: linear-gradient(180deg, #ffffff 0%, #f7f3ea 100%);
            border: 1.5px dashed #c89b3c;
            border-radius: 12px;
            display: flex;
            gap: 16px;
            padding: 16px;
        }

        .blog-image-preview {
            align-items: center;
            background: #fff;
            border: 1px solid #edf1f4;
            border-radius: 10px;
            display: flex;
            flex: 0 0 180px;
            height: 120px;
            justify-content: center;
            overflow: hidden;
        }

        .blog-image-preview img {
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .blog-detail-hero {
            background: linear-gradient(135deg, #f8fafc, #edf6f0);
            border: 1px solid #e4ece8;
            border-radius: 12px;
        }

        .blog-detail-image {
            border-radius: 12px;
            max-height: 260px;
            object-fit: cover;
            width: 100%;
        }

        .blog-detail-label {
            color: #6c757d;
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .blog-content-preview {
            line-height: 1.75;
        }

        .blog-content-preview img {
            height: auto;
            max-width: 100%;
        }

        @media (max-width: 575.98px) {
            #blogPostModal .modal-dialog,
            #blogPostDetailModal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            #blogPostModal .modal-content,
            #blogPostDetailModal .modal-content {
                max-height: calc(100vh - 16px);
            }

            .blog-upload-card {
                align-items: flex-start;
                flex-direction: column;
            }

            .blog-image-preview {
                flex-basis: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@push('js')
    <script>
        window.blogPostRoutes = {
            index: @json(route('admin.blog-posts.index')),
            list: @json(route('admin.blog-posts.list')),
        };
    </script>
    @vite(['resources/js/pages/blog-post.js'])
@endpush
