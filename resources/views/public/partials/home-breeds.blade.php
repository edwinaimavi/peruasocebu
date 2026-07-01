<section class="public-breeds-showcase js-reveal" id="razas">
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
                    $breedImageUrl = ! empty($breed->image_path ?? null) ? asset('storage/'.$breed->image_path) : null;
                    $descriptionTemplateId = 'breed-description-'.$loop->index;
                    $characteristicsTemplateId = 'breed-characteristics-'.$loop->index;
                @endphp
                <article class="public-breed-card js-reveal" data-breed-slide>
                    <div class="breed-visual {{ $breedImageUrl ? 'has-image' : '' }}">
                        <div class="breed-code-badge">{{ $breedCode ?: 'RAZA' }}</div>
                        @if ($breedImageUrl)
                            <img src="{{ $breedImageUrl }}" alt="{{ $breedName }}" class="breed-card-image" loading="lazy">
                        @else
                            <div class="breed-default-avatar">
                                <i class="fas fa-paw" aria-hidden="true"></i>
                                <span>Imagen no disponible</span>
                            </div>
                        @endif
                    </div>
                    <div class="breed-card-body">
                        <div class="breed-meta-row">
                            <span>{{ $statusLabel }}</span>
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
                            Ver mas <i class="fas fa-arrow-right" aria-hidden="true"></i>
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
