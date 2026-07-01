<section class="section why-section" id="nosotros">
    <div class="container">
        <div class="section-heading centered js-reveal">
            <span class="eyebrow"><span></span>Por que PERU ASOCEBU<span></span></span>
            <h2>Informacion ganadera confiable, organizada y verificable</h2>
            <p>Un portal institucional para conectar registros genealogicos, certificacion, trazabilidad y consulta publica sin exponer informacion sensible.</p>
        </div>

        <div class="benefit-grid">
            @foreach ([
                ['Genealogia verificable', 'Linajes, padre, madre y antecedentes disponibles para respaldar decisiones tecnicas.', 'fa-sitemap'],
                ['Certificacion digital', 'Documentos y codigos de verificacion para fortalecer la confianza del productor.', 'fa-certificate'],
                ['Historial del ganado', 'Datos publicos ordenados sobre registros, propietarios y trayectoria del ejemplar.', 'fa-clipboard-list'],
                ['Consulta segura', 'Informacion publica clara, cuidando datos internos y administrativos.', 'fa-shield-alt'],
            ] as [$title, $description, $icon])
                <article class="benefit-card js-reveal">
                    <span class="premium-icon"><i class="fas {{ $icon }}"></i></span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
