<section class="section features premium-services" id="servicios">
    <div class="container">
        <div class="section-heading centered js-reveal">
            <span class="eyebrow"><span></span>Servicios<span></span></span>
            <h2>Servicios para registrar, certificar y consultar ganado bovino</h2>
            <p>Herramientas orientadas a sostener informacion tecnica, administrativa y publica de cada ejemplar registrado.</p>
        </div>

        <div class="feature-grid">
            @foreach ([
                ['Registro genealogico', 'Identidad, procedencia, padre, madre y generaciones del animal.', 'fa-dna'],
                ['Certificacion de raza', 'Documentos de respaldo institucional para ejemplares registrados.', 'fa-award'],
                ['Certificacion de pureza', 'Soporte documental para pureza racial y valor genetico.', 'fa-medal'],
                ['Historial de propietarios', 'Trazabilidad de transferencias y propietario actual cuando corresponda.', 'fa-users'],
                ['Sanidad animal', 'Registro de vacunas, tratamientos y controles veterinarios.', 'fa-notes-medical'],
                ['Control reproductivo', 'Seguimiento de servicios, genealogia y planificacion genetica.', 'fa-venus-mars'],
                ['Consulta publica', 'Validacion de ganado, certificados y codigos de verificacion.', 'fa-search'],
                ['Blog ganadero', 'Noticias, actividades y conocimiento para productores y criadores.', 'fa-newspaper'],
            ] as [$title, $description, $icon])
                <article class="feature-card js-reveal">
                    <div class="feature-icon"><i class="fas {{ $icon }}"></i></div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $description }}</p>
                    <a href="{{ $title === 'Blog ganadero' ? route('public.blog.index') : '#contacto' }}">Solicitar informacion</a>
                </article>
            @endforeach
        </div>
    </div>
</section>
