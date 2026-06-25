@extends('adminlte::page')

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    $user = Auth::user();
    $rutaFoto =
        $user && $user->photo
            ? Storage::url($user->photo)
            : asset('vendor/adminlte/dist/img/logo2.png');
    $userRole = $user?->roles->first()?->name ?? 'Usuario';
@endphp

@section('content_top_nav_right')
    <li class="nav-item dropdown">
        <a class="nav-link navbar-user-trigger" data-toggle="dropdown" href="#" role="button"
            aria-label="Abrir menú de usuario">
            <span class="navbar-user-meta">
                <span class="navbar-user-name">{{ $user?->name ?? 'Usuario' }}</span>
                <span class="navbar-user-role">{{ $userRole }}</span>
            </span>
            <img src="{{ $rutaFoto }}" alt="Avatar de {{ $user?->name ?? 'usuario' }}"
                class="img-avatar-navbar">
        </a>

        <div class="dropdown-menu dropdown-menu-right navbar-user-menu">
            <a href="{{ route('settings.profile') }}" class="dropdown-item">
                <i class="fas fa-user-circle mr-2"></i> Mi perfil
            </a>
            <div class="dropdown-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                </button>
            </form>
        </div>
    </li>
@endsection

@section('content_header')
    @yield('header')
@stop

@section('content')
    <div id="divLoading" aria-live="polite" aria-label="Cargando">
        <div>
            <img src="{{ asset('images/loading.svg') }}" alt="Cargando..." />
        </div>
    </div>

    @yield('content_body')
@stop

@section('footer')
    <div class="float-right d-none d-sm-inline">
        Versión {{ config('app.version', '1.0.0') }}
    </div>

    <strong>PERU ASOCEBU</strong>
    <span class="ml-1">· Gestión Ganadera</span>
@stop

@push('js')
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function previewClientImage(event) {
            const input = event.target;

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#client_img_preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#btnGenerateFullName').on('click', function() {
            const firstName = ($('#first_name').val() || '').trim();
            const lastName = ($('#last_name').val() || '').trim();
            const fullName = `${firstName} ${lastName}`.trim();

            if (!fullName) {
                alert('Rellena nombres o apellidos para generar el nombre completo.');
                return;
            }

            $(this).removeClass('btn-outline-secondary').addClass('btn-success').text('Generado');
            setTimeout(() => {
                $(this)
                    .removeClass('btn-success')
                    .addClass('btn-outline-secondary')
                    .html('<i class="fas fa-sync-alt mr-1"></i> Generar Nombre');
            }, 1400);
        });

        $('#clientModal').on('shown.bs.modal', function() {
            $('#document_type').focus();
            $('#error-messages').addClass('d-none').empty();
        });
    </script>
@endpush

@push('css')
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/responsive.bootstrap4.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin-modern.css') }}">
@endpush
