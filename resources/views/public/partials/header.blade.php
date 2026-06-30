<header class="site-header" id="inicio">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('public.home') }}" aria-label="PERU ASOCEBU, pagina de inicio">
            <span class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 64 64" role="img">
                    <path d="M12 39c3-13 10-20 20-20s17 7 20 20c-5-5-10-7-15-7H27c-5 0-10 2-15 7Z"/>
                    <path d="M22 20 11 10c-1 8 2 14 10 17M42 20l11-10c1 8-2 14-10 17"/>
                    <path d="M25 34v8c0 6 14 6 14 0v-8M28 44h8"/>
                </svg>
            </span>
            <span>
                <strong>PERU ASOCEBU</strong>
                <small>Portal ganadero</small>
            </span>
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
            <span class="sr-only">Abrir menu</span>
        </button>

        <nav class="main-nav" id="main-nav" aria-label="Navegacion principal">
            <a href="{{ route('public.home') }}" class="{{ request()->routeIs('public.home') ? 'active' : '' }}">Inicio</a>
            <a href="{{ url('/#nosotros') }}">La asociacion</a>
            <a href="{{ url('/#servicios') }}">Servicios</a>
            <a href="{{ url('/#razas') }}">Razas</a>
            <a href="{{ url('/#registros') }}">Registros y certificados</a>
            <a href="{{ route('public.blog.index') }}" class="{{ request()->routeIs('public.blog.*') ? 'active' : '' }}">Blog / Noticias</a>
            <a href="{{ url('/#contacto') }}">Contacto</a>
        </nav>

        <div class="auth-actions">
            @auth
                <a class="btn btn-outline public-header-btn" href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}">Ir al panel</a>
            @else
                <a class="btn btn-primary btn-small public-header-btn" href="{{ route('login') }}">Iniciar sesion</a>
            @endauth
        </div>
    </div>
</header>
