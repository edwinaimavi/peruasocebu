<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('meta_description', 'PERU ASOCEBU: registro genealogico, trazabilidad y certificacion digital de ganado de raza.')">
    <meta name="theme-color" content="#123c2d">

    <title>@yield('title', 'PERU ASOCEBU')</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    @vite('resources/css/public-home.css')
    @stack('styles')
</head>
<body class="public-site @yield('body_class')">
    <a class="skip-link" href="#contenido">Ir al contenido</a>

    @include('public.partials.header')

    <main id="contenido" class="@yield('main_class')">
        @yield('content')
    </main>

    @include('public.partials.footer')
    @include('public.partials.scripts')
    @stack('scripts')
</body>
</html>
