<section class="hero public-hero" id="inicio">
    <div class="hero-pattern" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-copy js-reveal">
            <span class="eyebrow eyebrow-light"><span></span>Trazabilidad bovina confiable</span>
            <h1>Registro genealogico y certificacion de <em>ganado de raza</em></h1>
            <p class="hero-lead">Consulta animales, valida certificados, revisa genealogia y fortalece la trazabilidad ganadera del Peru desde una plataforma institucional.</p>

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

        <div class="hero-visual js-reveal" aria-label="Ganado Brahman PERU ASOCEBU">
            <div class="sun-disc"></div>
            <div class="hero-cattle-real-wrap">
                <img src="{{ asset('vendor/adminlte/dist/img/hero-brahman.png') }}" alt="Ganado Brahman PERU ASOCEBU" class="hero-cattle-real-img">
            </div>
            <div class="floating-card certificate-card">
                <span class="floating-icon"><i class="fas fa-certificate"></i></span>
                <span><small>Certificado</small><strong>Registro verificado</strong></span>
            </div>
            <div class="floating-card lineage-card">
                <span class="floating-icon"><i class="fas fa-sitemap"></i></span>
                <span><small>Linaje</small><strong>3 generaciones</strong></span>
            </div>
        </div>
    </div>

    <div class="container search-shell js-reveal" id="consulta">
        <form class="cattle-search hero-search-form" method="GET" action="{{ route('public.search') }}">
            <div class="search-heading">
                <span class="search-icon"><i class="fas fa-search"></i></span>
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
                        Buscar <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
