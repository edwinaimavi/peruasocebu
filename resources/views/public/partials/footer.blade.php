<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a class="brand brand-footer" href="{{ route('public.home') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 64 64"><path d="M12 39c3-13 10-20 20-20s17 7 20 20c-5-5-10-7-15-7H27c-5 0-10 2-15 7Z"/><path d="M22 20 11 10c-1 8 2 14 10 17M42 20l11-10c1 8-2 14-10 17"/><path d="M25 34v8c0 6 14 6 14 0v-8M28 44h8"/></svg>
                </span>
                <span><strong>PERU ASOCEBU</strong><small>Portal ganadero</small></span>
            </a>
            <p>Registro, trazabilidad y certificacion para una ganaderia bovina moderna y confiable.</p>
        </div>
        <div class="footer-links">
            <strong>Asociacion</strong>
            <a href="{{ url('/#nosotros') }}">Quienes somos</a>
            <a href="{{ url('/#servicios') }}">Criaderos</a>
            <a href="{{ url('/#servicios') }}">Propietarios</a>
            <a href="{{ url('/#contacto') }}">Contacto</a>
        </div>
        <div class="footer-links">
            <strong>Servicios</strong>
            <a href="{{ url('/#registros') }}">Registros</a>
            <a href="{{ url('/#registros') }}">Genealogia</a>
            <a href="{{ url('/#registros') }}">Certificados</a>
            <a href="{{ url('/#servicios') }}">Sanidad animal</a>
            <a href="{{ url('/#servicios') }}">Reproduccion</a>
        </div>
        <div class="footer-links">
            <strong>Contacto</strong>
            <a href="tel:+51999999999">+51 999 999 999</a>
            <a href="mailto:contacto@peruasocebu.pe">contacto@peruasocebu.pe</a>
            <a href="{{ url('/#contacto') }}">Peru</a>
            @auth
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}">Ir al panel</a>
            @else
                <a href="{{ route('login') }}">Iniciar sesion</a>
            @endauth
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} PERU ASOCEBU. Todos los derechos reservados.</span>
        <span>Desarrollando el futuro de la ganaderia peruana.</span>
    </div>
</footer>
