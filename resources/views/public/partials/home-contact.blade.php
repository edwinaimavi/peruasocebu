<section class="contact premium-contact" id="contacto">
    <div class="container contact-panel js-reveal">
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

            <div><span class="contact-icon"><i class="fas fa-phone"></i></span><span><small>Telefono</small><strong>+51 999 999 999</strong></span></div>
            <div><span class="contact-icon"><i class="fas fa-envelope"></i></span><span><small>Correo</small><strong>contacto@peruasocebu.pe</strong></span></div>
            <div><span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span><span><small>Ubicacion</small><strong>Peru</strong></span></div>
        </div>
    </div>
</section>
