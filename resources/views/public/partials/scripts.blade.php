<script src="{{ asset('vendor/sweetalert2/js/sweetalert2@11.js') }}"></script>
<script>
    const navToggle = document.querySelector('.nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    const publicContactForm = document.getElementById('publicContactForm');
    const heroSearchForm = document.querySelector('.hero-search-form');
    const typeSelect = heroSearchForm?.querySelector('[name="type"]');
    const queryInput = heroSearchForm?.querySelector('[name="q"]');

    const searchPlaceholders = {
        cattle_code: 'Ejemplo: GY001-000001',
        certificate_number: 'Ejemplo: CERT-2026-000001',
        verification_code: 'Ejemplo: VER-ABC123'
    };

    if (typeSelect && queryInput) {
        queryInput.placeholder = searchPlaceholders[typeSelect.value] || 'Ingrese su busqueda';

        typeSelect.addEventListener('change', function () {
            queryInput.placeholder = searchPlaceholders[this.value] || 'Ingrese su busqueda';
        });
    }

    heroSearchForm?.addEventListener('submit', (event) => {
        if (!queryInput?.value.trim()) {
            event.preventDefault();
            queryInput?.focus();
        }
    });

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

    const breedTrack = document.getElementById('breedSliderTrack');
    const breedSlides = Array.from(document.querySelectorAll('[data-breed-slide]'));
    const breedNext = document.querySelector('.breed-next');
    const breedPrev = document.querySelector('.breed-prev');
    const breedDots = document.getElementById('breedSliderDots');
    const breedPublicModal = document.getElementById('breedPublicModal');
    const breedModalTitle = document.getElementById('breedModalTitle');
    const breedModalCode = document.getElementById('breedModalCode');
    const breedModalOrigin = document.getElementById('breedModalOrigin');
    const breedModalDescription = document.getElementById('breedModalDescription');
    const breedModalCharacteristics = document.getElementById('breedModalCharacteristics');
    const breedModalStatus = document.getElementById('breedModalStatus');
    const breedModalImage = document.getElementById('breedModalImage');
    const breedModalFallback = document.getElementById('breedModalFallback');
    const breedModalFallbackCode = document.getElementById('breedModalFallbackCode');

    function breedScrollAmount() {
        const firstSlide = breedSlides[0];

        if (!firstSlide) {
            return 360;
        }

        return firstSlide.getBoundingClientRect().width + 24;
    }

    function setActiveBreed(index) {
        breedSlides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === index);
        });

        breedDots?.querySelectorAll('.breed-slider-dot').forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === index);
            dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
        });
    }

    function updateActiveBreedFromScroll() {
        if (!breedTrack || !breedSlides.length) {
            return;
        }

        const trackLeft = breedTrack.getBoundingClientRect().left;
        let activeIndex = 0;
        let closestDistance = Number.POSITIVE_INFINITY;

        breedSlides.forEach((slide, index) => {
            const distance = Math.abs(slide.getBoundingClientRect().left - trackLeft);

            if (distance < closestDistance) {
                closestDistance = distance;
                activeIndex = index;
            }
        });

        setActiveBreed(activeIndex);
    }

    if (breedTrack && breedSlides.length) {
        breedSlides.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'breed-slider-dot';
            dot.setAttribute('aria-label', `Ver raza ${index + 1}`);
            dot.addEventListener('click', () => {
                breedTrack.scrollTo({
                    left: breedSlides[index].offsetLeft - breedTrack.offsetLeft,
                    behavior: 'smooth'
                });
            });
            breedDots?.appendChild(dot);
        });

        setActiveBreed(0);

        breedNext?.addEventListener('click', () => {
            breedTrack.scrollBy({ left: breedScrollAmount(), behavior: 'smooth' });
        });

        breedPrev?.addEventListener('click', () => {
            breedTrack.scrollBy({ left: -breedScrollAmount(), behavior: 'smooth' });
        });

        breedTrack.addEventListener('scroll', () => {
            window.requestAnimationFrame(updateActiveBreedFromScroll);
        });
    }

    function closeBreedModal() {
        if (!breedPublicModal) {
            return;
        }

        breedPublicModal.classList.remove('is-open');
        breedPublicModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    document.querySelectorAll('.js-open-breed-modal').forEach((button) => {
        button.addEventListener('click', () => {
            if (!breedPublicModal) {
                return;
            }

            if (breedModalTitle) {
                breedModalTitle.textContent = button.dataset.name || 'Raza bovina';
            }

            if (breedModalCode) {
                breedModalCode.textContent = button.dataset.code || 'RAZ';
            }

            if (breedModalFallbackCode) {
                breedModalFallbackCode.textContent = 'Imagen no disponible';
            }

            if (breedModalOrigin) {
                breedModalOrigin.textContent = button.dataset.origin || 'Origen no registrado';
            }

            if (breedModalDescription) {
                setBreedModalHtml(
                    breedModalDescription,
                    button.dataset.descriptionTarget,
                    'Sin descripcion registrada.'
                );
            }

            if (breedModalCharacteristics) {
                setBreedModalHtml(
                    breedModalCharacteristics,
                    button.dataset.characteristicsTarget,
                    'Sin caracteristicas registradas.'
                );
            }

            if (breedModalStatus) {
                breedModalStatus.textContent = button.dataset.status || 'Activa';
            }

            if (breedModalImage && breedModalFallback) {
                if (button.dataset.image) {
                    breedModalImage.src = button.dataset.image;
                    breedModalImage.alt = `Imagen de ${button.dataset.name || 'raza bovina'}`;
                    breedModalImage.classList.remove('d-none');
                    breedModalFallback.classList.add('d-none');
                } else {
                    breedModalImage.src = '';
                    breedModalImage.alt = '';
                    breedModalImage.classList.add('d-none');
                    breedModalFallback.classList.remove('d-none');
                }
            }

            breedPublicModal.classList.add('is-open');
            breedPublicModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        });
    });

    function setBreedModalHtml(target, templateId, fallback) {
        const template = templateId ? document.getElementById(templateId) : null;

        if (template?.content) {
            target.innerHTML = '';
            target.appendChild(template.content.cloneNode(true));
            return;
        }

        target.textContent = fallback;
    }

    document.querySelectorAll('.js-close-breed-modal').forEach((button) => {
        button.addEventListener('click', closeBreedModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && breedPublicModal?.classList.contains('is-open')) {
            closeBreedModal();
        }
    });

    publicContactForm?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = publicContactForm.querySelector('.contact-submit');
        const buttonText = submitButton?.querySelector('span:first-child');
        const formData = new FormData(publicContactForm);

        publicContactForm.querySelectorAll('.contact-error').forEach((error) => {
            error.textContent = '';
        });
        publicContactForm.querySelectorAll('.is-invalid').forEach((field) => {
            field.classList.remove('is-invalid');
        });

        submitButton.disabled = true;
        if (buttonText) {
            buttonText.textContent = 'Enviando...';
        }

        try {
            const response = await fetch(publicContactForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const payload = await response.json();

            if (!response.ok) {
                if (response.status === 422 && payload.errors) {
                    Object.entries(payload.errors).forEach(([field, messages]) => {
                        const input = publicContactForm.querySelector(`[name="${field}"]`);
                        const error = publicContactForm.querySelector(`[data-error-for="${field}"]`);

                        input?.classList.add('is-invalid');
                        if (error) {
                            error.textContent = messages[0];
                        }
                    });
                }

                throw new Error(payload.message || 'No se pudo enviar el mensaje. Revisa los datos e intentalo nuevamente.');
            }

            publicContactForm.reset();
            Swal.fire({
                icon: 'success',
                title: 'Mensaje enviado',
                text: payload.message || 'Gracias por contactarnos. Hemos recibido tu mensaje y nos comunicaremos contigo pronto.',
                confirmButtonColor: '#1f4d36'
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'No se pudo enviar',
                text: error.message || 'No se pudo enviar el mensaje. Revisa los datos e intentalo nuevamente.',
                confirmButtonColor: '#1f4d36'
            });
        } finally {
            submitButton.disabled = false;
            if (buttonText) {
                buttonText.textContent = 'Enviar mensaje';
            }
        }
    });
</script>
