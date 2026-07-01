<section class="section registry premium-registry" id="registros">
    <div class="container registry-layout">
        <div class="section-heading light js-reveal">
            <span class="eyebrow eyebrow-light"><span></span>Registros y certificados</span>
            <h2>Valida informacion ganadera con respaldo institucional</h2>
            <p>El portal permite consultar ganado, validar certificados, revisar linaje y verificar datos publicos manteniendo la informacion sensible protegida.</p>
            <a class="btn btn-gold" href="#consulta">Validar certificado</a>
        </div>

        <div class="registry-grid">
            @foreach ([
                ['Consulta de ganado', 'Ubica ejemplares por codigo interno y revisa sus datos publicos principales.', 'fa-search'],
                ['Consulta de certificados', 'Valida certificados emitidos y su estado de verificacion.', 'fa-file-signature'],
                ['Validacion QR', 'Confirma autenticidad usando codigos de verificacion impresos o digitales.', 'fa-qrcode'],
                ['Genealogia', 'Revisa padre, madre y generaciones disponibles para respaldo racial.', 'fa-project-diagram'],
            ] as [$title, $description, $icon])
                <article class="registry-card js-reveal">
                    <span><i class="fas {{ $icon }}"></i></span>
                    <h3>{{ $title }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
