@extends('public.layouts.app')

@section('title', 'PERU ASOCEBU | Portal ganadero institucional')
@section('meta_description', 'PERU ASOCEBU: registro genealogico, trazabilidad y certificacion digital de ganado de raza.')

@section('content')
        <section class="hero">
            <div class="container hero-grid">
                <div class="hero-copy">
                    <span class="eyebrow eyebrow-light"><span></span>Trazabilidad bovina confiable</span>
                    <h1>Registro genealogico y certificacion de <em>ganado de raza</em></h1>
                    <p class="hero-lead">Un portal institucional para consultar animales, validar certificados, revisar genealogia y fortalecer la informacion ganadera del Peru.</p>

                    <div class="hero-actions">
                        <a class="btn btn-gold" href="#consulta">Consultar ganado</a>
                        <a class="btn btn-ghost" href="#registros">Ver certificados</a>
                    </div>

                    <div class="hero-trust">
                        <div><strong>Genealogia</strong><span>Linajes verificables</span></div>
                        <div><strong>Trazabilidad</strong><span>Historial organizado</span></div>
                        <div><strong>Certificacion</strong><span>Respaldo digital</span></div>
                    </div>
                </div>

                <div class="hero-visual" aria-label="Ilustracion ganadera institucional">
                    <div class="sun-disc"></div>
                    <div class="hero-cattle-real-wrap">
                        <img
                            src="{{ asset('vendor/adminlte/dist/img/hero-brahman.png') }}"
                            alt="Ganado Brahman PERU ASOCEBU"
                            class="hero-cattle-real-img">
                    </div>

                    <div class="floating-card certificate-card">
                        <span class="floating-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10a2 2 0 0 1 2 2v14l-4-2-3 2-3-2-4 2V5a2 2 0 0 1 2-2Z"/><path d="M8 8h8M8 12h5"/></svg></span>
                        <span><small>Certificado</small><strong>Registro verificado</strong></span>
                    </div>

                    <div class="floating-card lineage-card">
                        <span class="lineage-dot"></span>
                        <span><small>Linaje</small><strong>3 generaciones</strong></span>
                    </div>
                </div>
            </div>

            <div class="container search-shell" id="consulta">
                <form class="cattle-search hero-search-form" method="GET" action="{{ route('public.search') }}">
                    <div class="search-heading">
                        <span class="search-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg>
                        </span>
                        <span>
                            <strong>Consulta publica de ganado</strong>
                            <small>Busca por codigo del ganado, numero de certificado o codigo de verificacion.</small>
                        </span>
                    </div>
                    <div class="search-fields">
                        <label class="sr-only" for="search-type">Tipo de consulta</label>
                        <select id="search-type" name="type" required>
                            <option value="cattle_code" @selected(request('type') === 'cattle_code')>Codigo de ganado</option>
                            <option value="certificate_number" @selected(request('type') === 'certificate_number')>Numero de certificado</option>
                            <option value="verification_code" @selected(request('type') === 'verification_code')>Codigo de verificacion</option>
                        </select>
                        <label class="sr-only" for="cattle-code">Dato a consultar</label>
                        <div class="search-control">
                            <input id="cattle-code" name="q" type="search" placeholder="Ejemplo: GY001-000001" value="{{ request('q') }}" autocomplete="off" maxlength="100" required>
                            <button class="btn btn-gold" type="submit">
                                Buscar
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
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
                            <path d="M337 267c18-24 65-26 88-3l31 2c16 2 25 13 23 29l-4 29h-10l-2-29-21 19-5 38h-11l-4-40h-57l-5 40h-11l-3-50c-15-7-22-19-21-36Z" fill="#182f27"/>
                        </svg>
                    </div>
                    <div class="experience-badge">
                        <strong>Compromiso</strong>
                        <span>con la mejora genetica bovina</span>
                    </div>
                </div>

                <div class="section-copy">
                    <span class="eyebrow"><span></span>La asociacion</span>
                    <h2>Tradicion ganadera respaldada por informacion confiable</h2>
                    <p>PERU ASOCEBU fortalece la crianza responsable y el desarrollo genetico del ganado bovino mediante registros claros, trazables y preparados para consulta publica.</p>
                    <p>El sistema integra datos de campo, genealogia, sanidad, propietarios y certificados para dar confianza a criadores, compradores y equipos tecnicos.</p>
                    <div class="institution-grid">
                        <article><strong>Mision</strong><span>Impulsar registros bovinos verificables y utiles para el productor.</span></article>
                        <article><strong>Vision</strong><span>Ser referente nacional en trazabilidad y mejora genetica cebuina.</span></article>
                        <article><strong>Objetivo</strong><span>Conectar datos de campo, genealogia, sanidad y certificacion.</span></article>
                        <article><strong>Beneficios</strong><span>Mas confianza para criadores, propietarios, compradores y tecnicos.</span></article>
                    </div>
                    <a class="text-link" href="#servicios">Conoce nuestros servicios <span>&rarr;</span></a>
                </div>
            </div>
        </section>

        <section class="section features" id="servicios">
            <div class="container">
                <div class="section-heading centered">
                    <span class="eyebrow"><span></span>Servicios<span></span></span>
                    <h2>Un portal ganadero para gestionar, certificar y consultar</h2>
                    <p>Servicios orientados a sostener la informacion tecnica, administrativa y publica de cada ejemplar registrado.</p>
                </div>

                <div class="feature-grid">
                    @foreach ([
                        ['Registro genealogico', 'Identidad, procedencia, padre, madre y generaciones del animal.', 'M4 19V8l8-4 8 4v11M8 19v-5h8v5M3 19h18'],
                        ['Certificacion de raza', 'Documentos para respaldo institucional de ejemplares registrados.', 'M12 21s7-3.5 7-10V5l-7-2-7 2v6c0 6.5 7 10 7 10Z'],
                        ['Certificacion de pureza', 'Soporte documental para pureza racial y valor genetico.', 'M12 3l2.3 5 5.4.6-4 3.7 1.1 5.3L12 15.8 7.2 18.6l1.1-5.3-4-3.7 5.4-.6Z'],
                        ['Historial de propietarios', 'Trazabilidad de transferencias y propietario actual.', 'M16 11c1.7 0 3-1.3 3-3s-1.3-3-3-3 1.3-3 3 1.3 3 3 3ZM8 13c2.8 0 5 1.8 5 4v2H3v-2c0-2.2 2.2-4 5-4Z'],
                        ['Sanidad animal', 'Registro de vacunas, tratamientos y controles veterinarios.', 'M12 21C8 18 5 14.6 5 10a7 7 0 1 1 14 0c0 4.6-3 8-7 11ZM9 10h6M12 7v6'],
                        ['Control reproductivo', 'Seguimiento de servicios, genealogia y planificacion genetica.', 'M12 5v14M5 12h14M7 7l10 10M17 7 7 17'],
                        ['Directorio de criaderos', 'Informacion organizada para fundos, haciendas y productores.', 'M4 20V9l8-5 8 5v11M9 20v-6h6v6'],
                        ['Blog / noticias', 'Novedades institucionales, actividades y conocimiento ganadero.', 'M5 4h14v16H5ZM8 8h8M8 12h8M8 16h5'],
                    ] as [$title, $description, $icon])
                        <article class="feature-card">
                            <div class="feature-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icon }}"/></svg>
                            </div>
                            <h3>{{ $title }}</h3>
                            <p>{{ $description }}</p>
                            <a href="{{ $title === 'Blog / noticias' ? route('public.blog.index') : '#contacto' }}">Solicitar informacion</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="public-breeds-showcase" id="razas">
            <div class="public-section-header">
                <div>
                    <span><i></i>Razas</span>
                    <h2>Genetica bovina registrada</h2>
                </div>
                <p>Conoce las razas activas dentro del sistema, su origen, caracteristicas productivas y valor genetico.</p>
            </div>

            @php
                $fallbackBreeds = collect([
                    (object) ['name' => 'Gyr', 'description' => 'Reconocida aptitud lechera, docilidad y adaptacion a altas temperaturas.', 'characteristics' => 'Raza cebuina de doble proposito con enfasis lechero, buena fertilidad y temperamento docil.', 'origin_country' => 'India', 'code' => 'GYR', 'status' => 'active'],
                    (object) ['name' => 'Brahman', 'description' => 'Rusticidad, eficiencia productiva y excelente desempeno en campo.', 'characteristics' => 'Alta tolerancia al calor, resistencia en campo y buen rendimiento en sistemas tropicales.', 'origin_country' => 'Estados Unidos', 'code' => 'BRA', 'status' => 'active'],
                    (object) ['name' => 'Nelore', 'description' => 'Fortaleza, longevidad y eficiencia para sistemas de produccion de carne.', 'characteristics' => 'Destaca por habilidad materna, conversion eficiente y adaptacion a sistemas extensivos.', 'origin_country' => 'India / Brasil', 'code' => 'NEL', 'status' => 'active'],
                    (object) ['name' => 'Guzera', 'description' => 'Doble proposito, vigor y capacidad para prosperar en ambientes exigentes.', 'characteristics' => 'Buen desarrollo corporal, rusticidad y aptitud para leche y carne.', 'origin_country' => 'India', 'code' => 'GUZ', 'status' => 'active'],
                    (object) ['name' => 'Sindi', 'description' => 'Rusticidad, fertilidad y buen desempeno en sistemas de doble proposito.', 'characteristics' => 'Animal sobrio, resistente y eficiente para zonas calidas con recursos moderados.', 'origin_country' => 'Pakistan', 'code' => 'SIN', 'status' => 'active'],
                    (object) ['name' => 'Indubrasil', 'description' => 'Cruce cebuino de gran talla, fortaleza y adaptacion tropical.', 'characteristics' => 'Gran porte, orejas largas, docilidad y utilidad en programas de cruzamiento.', 'origin_country' => 'Brasil', 'code' => 'IND', 'status' => 'active'],
                ]);
                $displayBreeds = ($breeds ?? collect())->isNotEmpty() ? $breeds : $fallbackBreeds;
                $visualVariants = ['green', 'gold', 'cream', 'deep'];
                $cleanBreedHtml = function (?string $content, string $fallback): string {
                    $content = $content ?: '<p>'.$fallback.'</p>';
                    $content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content) ?? '';
                    $content = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $content) ?? '';
                    $content = preg_replace('/\son\w+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $content) ?? '';
                    $content = preg_replace('/javascript\s*:/is', '', $content) ?? '';

                    return strip_tags($content, '<p><br><strong><b><em><i><u><ul><ol><li><span><div><h1><h2><h3><h4><h5><h6><blockquote>');
                };
            @endphp

            <div class="breed-slider-shell" data-breed-slider>
                <button type="button" class="breed-slider-btn breed-prev" aria-label="Anterior">
                    <span class="breed-slider-arrow" aria-hidden="true">&lsaquo;</span>
                </button>

                <div class="breed-slider-track" id="breedSliderTrack">
                    @foreach ($displayBreeds as $breed)
                        @php
                            $breedName = $breed->name ?: 'Raza bovina';
                            $breedCode = $breed->code ?: \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($breedName, 0, 2));
                            $origin = \Illuminate\Support\Str::squish(strip_tags(html_entity_decode($breed->origin_country ?: 'Origen no registrado', ENT_QUOTES, 'UTF-8')));
                            $descriptionText = \Illuminate\Support\Str::squish(strip_tags(html_entity_decode($breed->description ?: '', ENT_QUOTES, 'UTF-8')));
                            $characteristicsText = \Illuminate\Support\Str::squish(strip_tags(html_entity_decode($breed->characteristics ?: '', ENT_QUOTES, 'UTF-8')));
                            $summary = \Illuminate\Support\Str::limit($descriptionText ?: $characteristicsText ?: 'Informacion de raza en actualizacion.', 132);
                            $descriptionHtml = $cleanBreedHtml($breed->description ?? null, 'Sin descripcion registrada.');
                            $characteristicsHtml = $cleanBreedHtml($breed->characteristics ?? null, 'Sin caracteristicas registradas.');
                            $statusLabel = ($breed->status ?? 'active') === 'active' ? 'Activa' : 'Inactiva';
                            $variant = $visualVariants[$loop->index % count($visualVariants)];
                            $breedImageUrl = ! empty($breed->image_path ?? null) ? asset('storage/'.$breed->image_path) : null;
                            $descriptionTemplateId = 'breed-description-'.$loop->index;
                            $characteristicsTemplateId = 'breed-characteristics-'.$loop->index;
                        @endphp
                        <article class="public-breed-card breed-card--{{ $variant }}" data-breed-slide>
                            <div class="breed-visual {{ $breedImageUrl ? 'has-image' : '' }}">
                                <div class="breed-code-badge">{{ $breedCode ?: 'RAZA' }}</div>
                                @if ($breedImageUrl)
                                    <img src="{{ $breedImageUrl }}" alt="{{ $breedName }}" class="breed-card-image">
                                @else
                                    <div class="breed-default-avatar">
                                        <i class="fas fa-paw" aria-hidden="true"></i>
                                        <span>Imagen no disponible</span>
                                    </div>
                                @endif
                            </div>
                            <div class="breed-card-body">
                                <div class="breed-meta-row">
                                    <span>{{ $breedCode }}</span>
                                    <span>Registro institucional</span>
                                </div>
                                <h3>{{ $breedName }}</h3>
                                <p class="breed-origin">{{ $origin }}</p>
                                <p class="breed-summary">{{ $summary }}</p>
                                <button type="button" class="breed-read-more js-open-breed-modal"
                                    data-name="{{ $breedName }}"
                                    data-code="{{ $breedCode }}"
                                    data-origin="{{ $origin }}"
                                    data-description-target="{{ $descriptionTemplateId }}"
                                    data-characteristics-target="{{ $characteristicsTemplateId }}"
                                    data-image="{{ $breedImageUrl }}"
                                    data-status="{{ $statusLabel }}">
                                    Ver m&aacute;s <i class="fas fa-arrow-right" aria-hidden="true"></i>
                                </button>
                                <template id="{{ $descriptionTemplateId }}">{!! $descriptionHtml !!}</template>
                                <template id="{{ $characteristicsTemplateId }}">{!! $characteristicsHtml !!}</template>
                            </div>
                        </article>
                    @endforeach
                </div>

                <button type="button" class="breed-slider-btn breed-next" aria-label="Siguiente">
                    <span class="breed-slider-arrow" aria-hidden="true">&rsaquo;</span>
                </button>

                <div class="breed-slider-dots" id="breedSliderDots" aria-label="Indicadores de razas"></div>
            </div>
        </section>

        <div class="breed-public-modal" id="breedPublicModal" aria-hidden="true">
            <div class="breed-public-modal-backdrop js-close-breed-modal"></div>

            <div class="breed-public-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="breedModalTitle">
                <button type="button" class="breed-public-modal-close js-close-breed-modal" aria-label="Cerrar detalle">&times;</button>

                <div class="breed-public-modal-visual">
                    <span id="breedModalCode">RAZ</span>
                    <img id="breedModalImage" class="breed-public-modal-image breed-modal-image d-none" src="" alt="">
                    <div id="breedModalFallback" class="breed-default-avatar breed-modal-default-avatar">
                        <i class="fas fa-paw" aria-hidden="true"></i>
                        <span id="breedModalFallbackCode">Imagen no disponible</span>
                    </div>
                </div>

                <div class="breed-public-modal-content">
                    <span class="breed-modal-eyebrow">Raza registrada</span>
                    <h3 id="breedModalTitle">Raza bovina</h3>
                    <p id="breedModalOrigin">Origen no registrado</p>
                    <span class="breed-modal-status" id="breedModalStatus">Activa</span>

                    <div class="breed-modal-section">
                        <h4>Descripcion</h4>
                        <div id="breedModalDescription">Sin descripcion registrada.</div>
                    </div>

                    <div class="breed-modal-section">
                        <h4>Caracteristicas</h4>
                        <div id="breedModalCharacteristics">Sin caracteristicas registradas.</div>
                    </div>
                </div>
            </div>
        </div>

        <section class="section registry" id="registros">
            <div class="container">
                <div class="section-heading centered light">
                    <span class="eyebrow eyebrow-light"><span></span>Registros y certificados<span></span></span>
                    <h2>Consulta publica para validar informacion ganadera</h2>
                    <p>El portal orienta la verificacion de ganado, certificados, propietario actual y genealogia, manteniendo la informacion sensible protegida dentro del panel administrativo.</p>
                </div>

                <div class="registry-grid">
                    @foreach ([
                        ['Consulta de ganado', 'Ubica ejemplares por codigo interno y revisa sus datos publicos principales.'],
                        ['Consulta de certificados', 'Valida certificados emitidos y su estado de verificacion.'],
                        ['Validacion con codigo QR', 'Confirma autenticidad usando codigos de verificacion impresos o digitales.'],
                        ['Propietario actual', 'Consulta referencias publicas del titular registrado cuando aplique.'],
                        ['Genealogia', 'Revisa padre, madre y generaciones disponibles para respaldo racial.'],
                    ] as [$title, $description])
                        <article class="registry-card">
                            <span></span>
                            <h3>{{ $title }}</h3>
                            <p>{{ $description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section news" id="blog">
            <div class="container">
                <div class="section-heading heading-row">
                    <div>
                        <span class="eyebrow"><span></span>Blog / Noticias</span>
                        <h2>Actualidad para productores y criadores</h2>
                    </div>
                    <a class="text-link" href="{{ route('public.blog.index') }}">Ver todas las noticias <span>&rarr;</span></a>
                </div>

                @if (($latestPosts ?? collect())->isEmpty())
                    <article class="news-card">
                        <div class="news-body">
                            <span class="category">Noticias</span>
                            <h3>Proximamente</h3>
                            <p>Proximamente compartiremos noticias y novedades de nuestro criadero.</p>
                        </div>
                    </article>
                @else
                    <div class="news-grid">
                        @foreach ($latestPosts as $post)
                            <article class="news-card">
                                @if ($post->image_path)
                                    <div class="news-art" style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($post->image_path) }}'); background-size: cover; background-position: center;"></div>
                                @else
                                    <div class="news-art certificate">
                                        <svg viewBox="0 0 320 180" aria-hidden="true"><path d="M112 25h96a12 12 0 0 1 12 12v111l-60-20-60 20V37a12 12 0 0 1 12-12Z"/><circle cx="160" cy="74" r="23"/><path d="m148 75 8 8 17-20"/></svg>
                                    </div>
                                @endif
                                <div class="news-body">
                                    <span class="category">{{ $post->published_at?->format('d/m/Y') ?: 'Noticias' }}</span>
                                    <h3>{{ $post->title }}</h3>
                                    <p>{{ $post->summary ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 130) }}</p>
                                    <a href="{{ route('public.blog.show', $post->slug) }}" aria-label="Leer mas sobre {{ $post->title }}">Leer mas <span>&rarr;</span></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="contact" id="contacto">
            <div class="container contact-panel">
                <div class="contact-copy">
                    <span class="eyebrow eyebrow-light"><span></span>Contacto</span>
                    <h2>Construyamos juntos una ganaderia con mas futuro</h2>
                    <p>Solicita informacion sobre registros, certificacion, razas, criaderos y servicios para propietarios.</p>
                    <a class="btn btn-gold" href="https://wa.me/51999999999" target="_blank" rel="noopener">Solicitar informacion <span>&rarr;</span></a>
                </div>
                <div class="contact-details">
                    <form class="public-contact-form" id="publicContactForm" method="POST" action="{{ route('public.contact.store') }}">
                        @csrf
                        <input type="text" name="website" autocomplete="off" tabindex="-1" class="contact-honeypot">

                        <div class="contact-form-grid">
                            <label><span>Nombre completo</span><input name="full_name" type="text" maxlength="255" required placeholder="Tu nombre"><small class="contact-error" data-error-for="full_name"></small></label>
                            <label><span>Telefono</span><input name="phone" type="tel" maxlength="30" placeholder="+51 999 999 999"><small class="contact-error" data-error-for="phone"></small></label>
                            <label><span>Correo</span><input name="email" type="email" maxlength="255" placeholder="correo@ejemplo.com"><small class="contact-error" data-error-for="email"></small></label>
                            <label><span>Asunto</span><input name="subject" type="text" maxlength="255" placeholder="Consulta sobre registros"><small class="contact-error" data-error-for="subject"></small></label>
                            <label class="contact-form-wide"><span>Mensaje</span><textarea name="message" maxlength="5000" rows="4" required placeholder="Cuentanos como podemos ayudarte"></textarea><small class="contact-error" data-error-for="message"></small></label>
                        </div>

                        <button class="btn btn-gold contact-submit" type="submit">
                            <span>Enviar mensaje</span>
                            <span aria-hidden="true">&rarr;</span>
                        </button>
                    </form>
                    <div><span class="contact-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h4l2 5-3 2c1 3 3 5 6 6l2-3 5 2v4c0 2-2 3-4 3C9 21 3 15 3 6c0-2 1-3 3-3Z"/></svg></span><span><small>Telefono</small><strong>+51 999 999 999</strong></span></div>
                    <div><span class="contact-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span><span><small>Correo</small><strong>contacto@peruasocebu.pe</strong></span></div>
                    <div><span class="contact-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span><span><small>Ubicacion</small><strong>Peru</strong></span></div>
                </div>
            </div>
        </section>
@endsection
