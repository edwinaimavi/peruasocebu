<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PERU ASOCEBU: registro genealógico, trazabilidad y certificación digital de ganado de raza.">
    <meta name="theme-color" content="#123c2d">

    <title>PERU ASOCEBU | Registro genealógico bovino</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite('resources/css/public-home.css')
</head>
<body>
    <a class="skip-link" href="#contenido">Ir al contenido</a>

    <header class="site-header" id="inicio">
        <div class="container nav-wrap">
            <a class="brand" href="#inicio" aria-label="PERU ASOCEBU, página de inicio">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 64 64" role="img">
                        <path d="M12 39c3-13 10-20 20-20s17 7 20 20c-5-5-10-7-15-7H27c-5 0-10 2-15 7Z"/>
                        <path d="M22 20 11 10c-1 8 2 14 10 17M42 20l11-10c1 8-2 14-10 17"/>
                        <path d="M25 34v8c0 6 14 6 14 0v-8M28 44h8"/>
                    </svg>
                </span>
                <span>
                    <strong>PERU ASOCEBU</strong>
                    <small>Genética que deja huella</small>
                </span>
            </a>

            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="main-nav">
                <span></span><span></span><span></span>
                <span class="sr-only">Abrir menú</span>
            </button>

            <nav class="main-nav" id="main-nav" aria-label="Navegación principal">
                <a href="#inicio">Inicio</a>
                <a href="#nosotros">Quiénes somos</a>
                <a href="#criadero">Nuestro criadero</a>
                <a href="#razas">Razas</a>
                <a href="#galeria">Galería</a>
                <a href="#blog">Blog</a>
                <a href="#contacto">Contacto</a>
            </nav>

            <div class="auth-actions">
                @auth
                    <a class="btn btn-outline" href="{{ url('/home') }}">Ir al panel</a>
                @else
                    <a class="login-link" href="{{ route('login') }}">Iniciar sesión</a>
                    @if (Route::has('register'))
                        <a class="btn btn-primary btn-small" href="{{ route('register') }}">Registrarse</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main id="contenido">
        <section class="hero">
            <div class="hero-orb hero-orb-one"></div>
            <div class="hero-orb hero-orb-two"></div>
            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow eyebrow-light">
                        <span></span>
                        Trazabilidad bovina confiable
                    </span>
                    <h1>Registro genealógico y certificación de <em>ganado de raza</em></h1>
                    <p class="hero-lead">Consulta información del ganado, linaje, pureza racial, propietarios y certificados desde una plataforma digital segura.</p>

                    <div class="hero-trust">
                        <div>
                            <strong>Genealogía</strong>
                            <span>Linajes verificables</span>
                        </div>
                        <div>
                            <strong>Trazabilidad</strong>
                            <span>Historial organizado</span>
                        </div>
                        <div>
                            <strong>Certificación</strong>
                            <span>Respaldo digital</span>
                        </div>
                    </div>
                </div>

                <div class="hero-visual" aria-label="Representación visual de registro bovino">
                    <div class="sun-disc"></div>
                    <svg class="cattle-illustration" viewBox="0 0 620 430" role="img" aria-label="Silueta estilizada de ganado cebú en el campo">
                        <defs>
                            <linearGradient id="cattleFill" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0" stop-color="#f1dfb4"/>
                                <stop offset="1" stop-color="#c69a4c"/>
                            </linearGradient>
                        </defs>
                        <path class="land land-back" d="M0 320c95-42 180-36 262-4 98 38 176 29 358-18v132H0Z"/>
                        <path class="land land-front" d="M0 355c130-40 245-22 334 15 93 39 178 34 286 4v56H0Z"/>
                        <g class="cattle">
                            <path d="M182 206c18-54 64-83 125-80 38 2 67 15 91 38 15 14 31 19 48 17 24-3 45 6 60 24 14 17 16 41 5 62l-18 33-14-9 10-32c5-17-1-32-18-43-7 32-25 56-54 71l-6 81h-21l-9-72-119 1-11 71h-21l-7-76c-33-14-54-40-62-78-14 3-27 0-39-9 23-2 43-9 60-19Z" fill="url(#cattleFill)"/>
                            <path d="M202 202c19-49 56-74 110-75 25 0 51 6 74 21-49-4-79 12-93 47-31-9-61-7-91 7Z" fill="#f7ebcf" opacity=".88"/>
                            <path d="M458 182c-5-22-1-39 13-51-1 20 6 35 21 45M439 185c-16-18-22-35-17-52 7 18 18 31 34 39" fill="none" stroke="#e5c176" stroke-width="8" stroke-linecap="round"/>
                            <path d="M463 208c16 3 27 12 34 27M207 278c54 16 115 18 181 5" fill="none" stroke="#9d7437" stroke-width="5" stroke-linecap="round" opacity=".65"/>
                            <circle cx="471" cy="205" r="4" fill="#173f30"/>
                        </g>
                        <g class="grass" stroke="#ddbc73" stroke-width="4" stroke-linecap="round">
                            <path d="M88 370v-27m0 16-11-12m11 7 12-14M540 376v-32m0 17-13-14m13 7 13-17M141 388v-22m0 10-10-10"/>
                        </g>
                    </svg>

                    <div class="floating-card certificate-card">
                        <span class="floating-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v14l-4-2-3 2-3-2-4 2V5a2 2 0 0 1 2-2Z"/><path d="M8 8h8M8 12h5"/></svg>
                        </span>
                        <span><small>Certificado</small><strong>Registro verificado</strong></span>
                        <b>✓</b>
                    </div>

                    <div class="floating-card lineage-card">
                        <span class="lineage-dot"></span>
                        <span><small>Linaje</small><strong>3 generaciones</strong></span>
                    </div>
                </div>
            </div>

            <div class="container search-shell">
                {{-- Futuro: cambiar action por route('cattle.public.search') cuando exista /consulta-ganado. --}}
                <form class="cattle-search" method="GET" action="{{ url('/') }}">
                    <div class="search-heading">
                        <span class="search-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                        </span>
                        <span>
                            <strong>Consulta pública de ganado</strong>
                            <small>Datos públicos, genealogía y certificado del animal.</small>
                        </span>
                    </div>
                    <label class="sr-only" for="cattle-code">Código del ganado</label>
                    <div class="search-control">
                        <input id="cattle-code" name="codigo" type="search" placeholder="Ingrese código del ganado, ejemplo: CEBU-000123" value="{{ request('codigo') }}" autocomplete="off">
                        <button class="btn btn-gold" type="submit">
                            Buscar ganado
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="section about" id="nosotros">
            <div class="container two-columns">
                <div class="about-visual">
                    <div class="pasture-card">
                        <svg viewBox="0 0 560 420" role="img" aria-label="Paisaje de hacienda y ganado">
                            <rect width="560" height="420" fill="#e9dfc8"/>
                            <circle cx="430" cy="92" r="52" fill="#d3a958" opacity=".75"/>
                            <path d="M0 237c100-50 186-34 266 1 99 44 175 31 294-15v197H0Z" fill="#78916e"/>
                            <path d="M0 294c105-39 214-29 317 20 82 39 157 38 243 8v98H0Z" fill="#254d3d"/>
                            <path d="M52 277h188M81 250v93m47-103v93m55-79v95" stroke="#d8c7a3" stroke-width="8" opacity=".85"/>
                            <g fill="#182f27">
                                <path d="M337 267c18-24 65-26 88-3l31 2c16 2 25 13 23 29l-4 29h-10l-2-29-21 19-5 38h-11l-4-40h-57l-5 40h-11l-3-50c-15-7-22-19-21-36Z"/>
                                <path d="m454 266 15-18c1 13-2 22-10 27Z"/>
                            </g>
                        </svg>
                    </div>
                    <div class="experience-badge">
                        <strong>Compromiso</strong>
                        <span>con la mejora genética bovina</span>
                    </div>
                </div>

                <div class="section-copy">
                    <span class="eyebrow"><span></span>Quiénes somos</span>
                    <h2>Tradición ganadera respaldada por información confiable</h2>
                    <p>PERU ASOCEBU nace para fortalecer la crianza responsable y el desarrollo genético del ganado bovino. Integramos experiencia de campo y herramientas digitales para mantener cada registro claro, disponible y seguro.</p>
                    <p>Nuestro propósito es brindar confianza a criadores, propietarios y compradores mediante información trazable sobre el origen, la evolución y la calidad racial de cada animal.</p>
                    <ul class="check-list">
                        <li><span>✓</span> Registro ordenado de identidad y procedencia</li>
                        <li><span>✓</span> Seguimiento de linajes y mejora genética</li>
                        <li><span>✓</span> Información preparada para verificación pública</li>
                    </ul>
                    <a class="text-link" href="#criadero">Conoce nuestro enfoque <span>→</span></a>
                </div>
            </div>
        </section>

        <section class="section features" id="criadero">
            <div class="container">
                <div class="section-heading centered">
                    <span class="eyebrow"><span></span>Nuestro criadero<span></span></span>
                    <h2>Gestión responsable en cada etapa</h2>
                    <p>Una visión integral para proteger el bienestar animal, conservar la historia de cada ejemplar y tomar mejores decisiones.</p>
                </div>

                <div class="feature-grid">
                    <article class="feature-card">
                        <span class="card-number">01</span>
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V8l8-4 8 4v11M8 19v-5h8v5M3 19h18"/></svg>
                        </div>
                        <h3>Control de ganado</h3>
                        <p>Identificación y organización de la información esencial de cada animal del hato.</p>
                    </article>
                    <article class="feature-card featured">
                        <span class="card-number">02</span>
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-3.5 7-10V5l-7-2-7 2v6c0 6.5 7 10 7 10Z"/><path d="m9 12 2 2 4-5"/></svg>
                        </div>
                        <h3>Manejo responsable</h3>
                        <p>Prácticas orientadas al bienestar, la sanidad y el desarrollo sostenible del ganado.</p>
                    </article>
                    <article class="feature-card">
                        <span class="card-number">03</span>
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="5" r="2"/><circle cx="6" cy="18" r="2"/><circle cx="18" cy="18" r="2"/><path d="M12 7v4M6 16v-2h12v2M12 11v3"/></svg>
                        </div>
                        <h3>Historial genealógico</h3>
                        <p>Registro de padre, madre y generaciones para comprender el valor de cada linaje.</p>
                    </article>
                    <article class="feature-card">
                        <span class="card-number">04</span>
                        <div class="feature-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="9" r="6"/><path d="m8 14-1 7 5-3 5 3-1-7M9 9l2 2 4-4"/></svg>
                        </div>
                        <h3>Certificación de raza</h3>
                        <p>Base documental para respaldar pureza racial, propiedad y autenticidad del ejemplar.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section breeds" id="razas">
            <div class="container">
                <div class="section-heading heading-row">
                    <div>
                        <span class="eyebrow"><span></span>Razas que trabajamos</span>
                        <h2>Genética adaptada, productiva y de gran valor</h2>
                    </div>
                    <p>Registramos ejemplares de razas cebuinas reconocidas por su adaptación, rusticidad y aporte a la ganadería tropical.</p>
                </div>

                <div class="breed-grid">
                    @foreach ([
                        ['Cebú', 'Base genética de gran adaptación al clima tropical y notable resistencia.', 'C'],
                        ['Brahman', 'Rusticidad, eficiencia productiva y excelente desempeño en campo.', 'B'],
                        ['Gyr', 'Reconocida aptitud lechera, docilidad y adaptación a altas temperaturas.', 'G'],
                        ['Nelore', 'Fortaleza, longevidad y eficiencia para sistemas de producción de carne.', 'N'],
                        ['Guzerá', 'Doble propósito, vigor y capacidad para prosperar en ambientes exigentes.', 'GZ'],
                    ] as [$name, $description, $initials])
                        <article class="breed-card">
                            <div class="breed-art" aria-hidden="true">
                                <span>{{ $initials }}</span>
                                <svg viewBox="0 0 180 120"><path d="M30 71c14-30 40-43 77-38 18 3 31 11 41 23l20 3c9 2 14 9 11 19l-7 22h-7l-2-22-14 18-4 20h-8l-3-24H64l-3 24h-8l-3-31c-12-5-19-14-20-27Z"/><path d="m148 57 10-16c2 11 0 18-7 23Z"/></svg>
                            </div>
                            <div class="breed-body">
                                <h3>{{ $name }}</h3>
                                <p>{{ $description }}</p>
                                <span>Conocer la raza <b>→</b></span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section gallery" id="galeria">
            <div class="container">
                <div class="section-heading centered light">
                    <span class="eyebrow eyebrow-light"><span></span>Nuestra galería<span></span></span>
                    <h2>El campo, nuestra mejor carta de presentación</h2>
                    <p>Una muestra visual de los ejemplares, el entorno y el cuidado que definen nuestro trabajo.</p>
                </div>

                <div class="gallery-grid">
                    <article class="gallery-item gallery-large">
                        <div class="gallery-scene scene-one">
                            <svg viewBox="0 0 500 340" aria-hidden="true"><circle cx="389" cy="74" r="42"/><path class="hill" d="M0 210c93-53 190-51 279-8 70 33 144 38 221 4v134H0Z"/><path class="cow" d="M129 208c26-46 90-52 129-16l52 3c23 2 36 18 31 40l-9 51h-16l-4-48-29 28-7 57h-16l-6-60h-91l-7 60h-16l-5-75c-23-11-34-28-32-53Z"/></svg>
                        </div>
                        <div class="gallery-caption"><span>Selección genética</span><strong>Ejemplares con historia y proyección</strong></div>
                    </article>
                    <article class="gallery-item">
                        <div class="gallery-scene scene-two">
                            <svg viewBox="0 0 300 220" aria-hidden="true"><path class="hill" d="M0 132c76-45 147-41 210-8 31 17 61 21 90 15v81H0Z"/><path class="cow" d="M70 133c17-30 58-34 84-10l34 2c15 1 24 12 21 26l-6 34h-11l-2-32-19 19-5 38h-10l-4-40H92l-5 40H77l-3-50c-16-7-23-19-21-35Z"/></svg>
                        </div>
                        <div class="gallery-caption"><span>Bienestar animal</span><strong>Cuidado en cada etapa</strong></div>
                    </article>
                    <article class="gallery-item">
                        <div class="gallery-scene scene-three">
                            <svg viewBox="0 0 300 220" aria-hidden="true"><circle cx="238" cy="51" r="30"/><path class="hill" d="M0 145c76-45 147-41 210-8 31 17 61 21 90 15v68H0Z"/><path class="fence" d="M18 154h151M34 132v72m46-81v72m48-64v74"/><path class="cow" d="M166 145c10-20 39-22 56-7l25 1c10 1 15 8 13 18l-5 24h-7l-1-23-13 13-3 27h-8l-2-28h-41l-3 28h-8l-2-35c-10-5-15-13-13-23Z"/></svg>
                        </div>
                        <div class="gallery-caption"><span>Nuestro entorno</span><strong>Campo y manejo responsable</strong></div>
                    </article>
                </div>
            </div>
        </section>

        <section class="section news" id="blog">
            <div class="container">
                <div class="section-heading heading-row">
                    <div>
                        <span class="eyebrow"><span></span>Actualidad ganadera</span>
                        <h2>Conocimiento para criar mejor</h2>
                    </div>
                    <a class="text-link" href="#blog">Ver todas las noticias <span>→</span></a>
                </div>

                <div class="news-grid">
                    @foreach ([
                        ['Genealogía', 'Importancia del árbol genealógico en el ganado', 'Conocer el origen permite planificar cruces, conservar cualidades y reducir riesgos genéticos.', 'tree'],
                        ['Genética', 'Cómo verificar la pureza racial', 'Los registros, antecedentes y evaluaciones aportan evidencia para identificar la composición racial.', 'dna'],
                        ['Tecnología', 'Beneficios de la certificación digital', 'Un certificado verificable facilita la consulta, protege la información y genera mayor confianza.', 'certificate'],
                    ] as [$category, $title, $excerpt, $icon])
                        <article class="news-card">
                            <div class="news-art {{ $icon }}">
                                @if ($icon === 'tree')
                                    <svg viewBox="0 0 320 180" aria-hidden="true"><circle cx="160" cy="38" r="16"/><circle cx="82" cy="132" r="16"/><circle cx="160" cy="132" r="16"/><circle cx="238" cy="132" r="16"/><path d="M160 54v35M82 116V96h156v20M160 89v27"/></svg>
                                @elseif ($icon === 'dna')
                                    <svg viewBox="0 0 320 180" aria-hidden="true"><path d="M109 30c75 31 27 90 102 120M211 30c-75 31-27 90-102 120M125 48h70M109 75h102M109 106h102M125 133h70"/></svg>
                                @else
                                    <svg viewBox="0 0 320 180" aria-hidden="true"><path d="M112 25h96a12 12 0 0 1 12 12v111l-60-20-60 20V37a12 12 0 0 1 12-12Z"/><circle cx="160" cy="74" r="23"/><path d="m148 75 8 8 17-20"/></svg>
                                @endif
                            </div>
                            <div class="news-body">
                                <span class="category">{{ $category }}</span>
                                <h3>{{ $title }}</h3>
                                <p>{{ $excerpt }}</p>
                                <a href="#contacto" aria-label="Leer más sobre {{ $title }}">Leer artículo <span>→</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="contact" id="contacto">
            <div class="container contact-panel">
                <div class="contact-copy">
                    <span class="eyebrow eyebrow-light"><span></span>Hablemos</span>
                    <h2>Construyamos juntos una ganadería con más futuro</h2>
                    <p>Solicita información sobre registros, certificación y servicios para criadores y propietarios.</p>
                    <a class="btn btn-gold" href="https://wa.me/51999999999" target="_blank" rel="noopener">
                        Solicitar información
                        <span>→</span>
                    </a>
                </div>
                <div class="contact-details">
                    <div>
                        <span class="contact-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h4l2 5-3 2c1 3 3 5 6 6l2-3 5 2v4c0 2-2 3-4 3C9 21 3 15 3 6c0-2 1-3 3-3Z"/></svg>
                        </span>
                        <span><small>Teléfono</small><strong>+51 999 999 999</strong></span>
                    </div>
                    <div>
                        <span class="contact-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
                        </span>
                        <span><small>Correo</small><strong>contacto@peruasocebu.pe</strong></span>
                    </div>
                    <div>
                        <span class="contact-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                        </span>
                        <span><small>Ubicación</small><strong>Perú</strong></span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a class="brand brand-footer" href="#inicio">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 64 64"><path d="M12 39c3-13 10-20 20-20s17 7 20 20c-5-5-10-7-15-7H27c-5 0-10 2-15 7Z"/><path d="M22 20 11 10c-1 8 2 14 10 17M42 20l11-10c1 8-2 14-10 17"/><path d="M25 34v8c0 6 14 6 14 0v-8M28 44h8"/></svg>
                    </span>
                    <span><strong>PERU ASOCEBU</strong><small>Genética que deja huella</small></span>
                </a>
                <p>Registro, trazabilidad y certificación para una ganadería bovina moderna y confiable.</p>
            </div>
            <div class="footer-links">
                <strong>Enlaces rápidos</strong>
                <a href="#nosotros">Quiénes somos</a>
                <a href="#criadero">Nuestro criadero</a>
                <a href="#razas">Razas</a>
                <a href="#galeria">Galería</a>
            </div>
            <div class="footer-links">
                <strong>Plataforma</strong>
                <a href="#contenido">Consulta de ganado</a>
                <a href="{{ route('login') }}">Iniciar sesión</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Crear una cuenta</a>
                @endif
                <a href="#contacto">Contacto</a>
            </div>
            <div class="footer-note">
                <strong>Próximamente</strong>
                <p>Certificados verificables, códigos QR y consulta completa del árbol genealógico bovino.</p>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ date('Y') }} PERU ASOCEBU. Todos los derechos reservados.</span>
            <span>Desarrollando el futuro de la ganadería peruana.</span>
        </div>
    </footer>

    <script>
        const navToggle = document.querySelector('.nav-toggle');
        const mainNav = document.querySelector('.main-nav');

        navToggle?.addEventListener('click', () => {
            const isOpen = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!isOpen));
            mainNav.classList.toggle('is-open');
        });

        document.querySelectorAll('.main-nav a').forEach((link) => {
            link.addEventListener('click', () => {
                navToggle?.setAttribute('aria-expanded', 'false');
                mainNav?.classList.remove('is-open');
            });
        });
    </script>
</body>
</html>
