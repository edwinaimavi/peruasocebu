var divLoading = document.getElementById('divLoading');
let tableCattle;
let cattleSubmitting = false;
let cattlePhotoObjectUrl = null;
let currentCattlePhotoUrl = null;
let galleryPhotoObjectUrls = [];
let currentDetailCattleId = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableCattle = $('#tableCattle').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.cattleRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name', render: $.fn.dataTable.render.text() },
            { data: 'breed_name', name: 'breed.name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'sex', name: 'sex', orderable: false },
            { data: 'ranch_name', name: 'ranch.name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'owner_name', name: 'currentOwner.full_name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'purity_percentage', name: 'purity_percentage', defaultContent: '—' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'sale_status', name: 'sale_status', orderable: false },
            { data: 'is_public', name: 'is_public', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        order: [[1, 'desc']],
        language: {
            url: '/vendor/datatables/js/i18n/es-ES.json'
        },
        dom: `
            <'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
            <'row'<'col-sm-12'tr>>
            <'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-center justify-content-md-end'p>>
            <'row mt-3'<'col-sm-12 text-center'B>>
        `,
        buttons: [
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel"></i> Excel',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                className: 'btn btn-secondary btn-sm',
                text: '<i class="fas fa-print"></i> Imprimir',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ],
        preDrawCallback: showLoading,
        drawCallback: hideLoading
    });

    $('#breed_id').on('change', updateCattleCodePreview);
    $('#main_photo').on('change', handlePhotoChange);
    $('#gallery_photos').on('change', handleGalleryPhotoChange);
    $('#btnSelectGalleryPhotos').on('click', prepareGalleryPhotoPicker);
    $('#btnRemoveCattlePhotoPreview').on('click', clearSelectedCattlePhoto);
    $('#btnOpenAddCattlePhoto').on('click', openAddCattlePhotoModal);
    $('#cattlePhotoForm').on('submit', submitCattlePhotoForm);

    $(document).on('click', '.viewCattlePhoto', viewCattlePhoto);
    $(document).on('click', '.editCattlePhoto', editCattlePhoto);
    $(document).on('click', '.deleteCattlePhoto', deleteCattlePhoto);
    $(document).on('click', '.setMainCattlePhoto', setMainCattlePhoto);

    $('#cattleForm').on('submit', function (event) {
        event.preventDefault();

        if (cattleSubmitting) {
            return;
        }

        clearValidation();

        if (!validateCattleFormBeforeSubmit()) {
            return;
        }

        cattleSubmitting = true;
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const cattleId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.cattleRoutes.index;
        appendGalleryPhotos(formData);

        if (!$('#is_public').is(':checked')) {
            formData.set('is_public', '0');
        }

        if (cattleId) {
            url = `${window.cattleRoutes.index}/${cattleId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#cattleModal').modal('hide');
                tableCattle.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el ganado. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                cattleSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editCattle', function () {
        const cattleId = $(this).data('id');
        showLoading();

        $.get(`${window.cattleRoutes.index}/${cattleId}`)
            .done(function (response) {
                prepareEditForm(response.cattle);
                $('#cattleModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del ganado.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewCattle', function () {
        const cattleId = $(this).data('id');
        showLoading();

        $.get(`${window.cattleRoutes.index}/${cattleId}`)
            .done(function (response) {
                fillDetailModal(response.cattle);
                $('#cattleDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del ganado.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteCattle', function () {
        const cattleId = $(this).data('id');
        const cattleName = $(this).data('name') || 'este registro';

        Swal.fire({
            title: '¿Eliminar ganado?',
            text: `Se eliminará "${cattleName}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            showLoading();

            $.ajax({
                url: `${window.cattleRoutes.index}/${cattleId}`,
                type: 'DELETE',
                success: function (response) {
                    tableCattle.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el ganado.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#cattleModal').on('show.bs.modal', function () {
        if (!$('#cattleForm').attr('data-id')) {
            resetCattleForm();
        }
    });

    $('#cattleModal').on('shown.bs.modal', function () {
        $('#name').trigger('focus');
    });

    $('#cattleModal').on('hidden.bs.modal', resetCattleForm);
});

function prepareEditForm(cattle) {
    resetCattleForm();

    $('#cattleForm').attr('data-id', cattle.id);
    $('#cattleModalLabel').text('Editar Ganado');
    $('#saveCattleButton span').text('Actualizar Ganado');

    [
        'code', 'name', 'breed_id', 'ranch_id', 'current_owner_id', 'father_id',
        'mother_id', 'sex', 'birth_date', 'color', 'weight_kg', 'height_cm',
        'ear_tag', 'chip_number', 'purity_percentage', 'status', 'sale_status',
        'observations'
    ].forEach(function (field) {
        $(`#${field}`).val(cattle[field] ?? '');
    });

    $('#is_public').prop('checked', Boolean(cattle.is_public));
    currentCattlePhotoUrl = cattle.photo_url || null;
    setCattlePhotoPreview(currentCattlePhotoUrl);
    renderExistingGallery(cattle.photos || []);
}

function handlePhotoChange() {
    const file = this.files && this.files[0];

    if (!file) {
        setCattlePhotoPreview(null);
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    setCattlePhotoPreview(objectUrl, {
        fileName: file.name,
        isObjectUrl: true,
        removable: true
    });
}

function setCattlePhotoPreview(url, options = {}) {
    if (cattlePhotoObjectUrl) {
        URL.revokeObjectURL(cattlePhotoObjectUrl);
        cattlePhotoObjectUrl = null;
    }

    if (options.isObjectUrl) {
        cattlePhotoObjectUrl = url;
    }

    const $preview = $('#mainPhotoPreview');
    const $placeholder = $('#mainPhotoPlaceholder');
    const $fileName = $('#mainPhotoFileName');
    const $removeButton = $('#btnRemoveCattlePhotoPreview');

    if (!url) {
        $preview.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        $fileName.text('Ningún archivo seleccionado');
        $removeButton.addClass('d-none');
        return;
    }

    $preview.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
    $fileName.text(options.fileName || 'Foto actual');
    $removeButton.toggleClass('d-none', !options.removable);
}

function clearSelectedCattlePhoto() {
    $('#main_photo').val('');
    setCattlePhotoPreview(currentCattlePhotoUrl);
}

function prepareGalleryPhotoPicker() {
    const galleryInput = document.getElementById('gallery_photos');

    if (galleryInput) {
        galleryInput.multiple = true;
        galleryInput.name = 'gallery_photos[]';
    }
}

function appendGalleryPhotos(formData) {
    const galleryInput = document.getElementById('gallery_photos');

    formData.delete('gallery_photos');
    formData.delete('gallery_photos[]');

    if (!galleryInput || !galleryInput.files) {
        return;
    }

    Array.from(galleryInput.files).forEach(function (file) {
        formData.append('gallery_photos[]', file);
    });
}

function handleGalleryPhotoChange() {
    clearGalleryPhotoPreview();

    const files = Array.from(this.files || []);

    if (files.length > 0) {
        $('#galleryPhotoPreview').append(`
            <div class="cattle-gallery-selection-summary">
                ${files.length} foto${files.length === 1 ? '' : 's'} seleccionada${files.length === 1 ? '' : 's'}
            </div>
        `);
    }

    files.forEach(function (file) {
        const objectUrl = URL.createObjectURL(file);
        galleryPhotoObjectUrls.push(objectUrl);
        $('#galleryPhotoPreview').append(`
            <div class="cattle-gallery-preview-item">
                <img src="${objectUrl}" alt="${escapeHtml(file.name)}">
                <div class="cattle-gallery-item-body small text-muted">${escapeHtml(file.name)}</div>
            </div>
        `);
    });

    if (files.length > 0) {
        $('#gallery_photos-error').text('');
    }
}

function renderExistingGallery(photos) {
    clearGalleryPhotoPreview();

    (photos || []).forEach(function (photo) {
        $('#galleryPhotoPreview').append(`
            <div class="cattle-gallery-preview-item">
                <img src="${escapeHtml(photo.image_url || '')}" alt="${escapeHtml(photo.title || 'Foto')}">
                <div class="cattle-gallery-item-body small">
                    ${photo.is_main ? '<span class="badge badge-success mb-1">Principal</span>' : ''}
                    <div>${escapeHtml(photo.title || 'Foto del ganado')}</div>
                </div>
            </div>
        `);
    });
}

function clearGalleryPhotoPreview() {
    galleryPhotoObjectUrls.forEach(function (url) {
        URL.revokeObjectURL(url);
    });
    galleryPhotoObjectUrls = [];
    $('#galleryPhotoPreview').empty();
}

function updateCattleCodePreview() {
    const cattleId = $('#cattleForm').attr('data-id');

    if (cattleId && $('#code').val()) {
        return;
    }

    const breed = selectedBreed();

    if (!breed) {
        $('#code').val('');
        return;
    }

    $('#code').val(`${breedCodePrefix(breed)}-000001`);
}

function selectedBreed() {
    const breedId = Number($('#breed_id').val());

    return (window.cattleBreeds || []).find(function (breed) {
        return Number(breed.id) === breedId;
    });
}

function validateCattleFormBeforeSubmit() {
    const cattleId = $('#cattleForm').attr('data-id');
    const fatherId = $('#father_id').val();
    const motherId = $('#mother_id').val();
    const fatherSex = $('#father_id option:selected').data('sex');
    const motherSex = $('#mother_id option:selected').data('sex');
    const errors = {};

    if (fatherId && motherId && String(fatherId) === String(motherId)) {
        errors.mother_id = ['El padre y la madre no pueden ser el mismo animal.'];
    }

    if (cattleId && fatherId && String(cattleId) === String(fatherId)) {
        errors.father_id = ['El familiar no puede ser el mismo animal principal.'];
    }

    if (cattleId && motherId && String(cattleId) === String(motherId)) {
        errors.mother_id = ['El familiar no puede ser el mismo animal principal.'];
    }

    if (fatherId && fatherSex && fatherSex !== 'male') {
        errors.father_id = ['El padre seleccionado debe ser un animal macho.'];
    }

    if (motherId && motherSex && motherSex !== 'female') {
        errors.mother_id = ['La madre seleccionada debe ser un animal hembra.'];
    }

    if (Object.keys(errors).length) {
        showValidationErrors(errors);
        return false;
    }

    return true;
}

function breedCodePrefix(breed) {
    const base = breed.code || breed.name || 'GAN';
    const prefix = String(base)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-zA-Z0-9]/g, '')
        .toUpperCase();

    return prefix || 'GAN';
}

function fillDetailModal(cattle) {
    currentDetailCattleId = cattle.id;
    $('#detailCattleSubtitle').text(`Registro #${cattle.id}`);
    $('#detailName').text(valueOrDash(cattle.name));
    $('#detailCode').text(valueOrDash(cattle.code));
    $('#detailBreed').text(valueOrDash(cattle.breed_name));
    $('#detailBirthDate').text(valueOrDash(cattle.birth_date_formatted));
    $('#detailAge').text(valueOrDash(cattle.age_text || cattle.age_label));
    $('#detailColor').text(valueOrDash(cattle.color));
    $('#detailRanch').text(valueOrDash(cattle.ranch_name));
    $('#detailOwner').text(valueOrDash(cattle.owner_name));
    $('#detailPurity').text(cattle.purity_percentage !== null && cattle.purity_percentage !== undefined ? `${cattle.purity_percentage}%` : '—');
    $('#detailWeight').text(cattle.weight_kg ? `${cattle.weight_kg} kg` : '—');
    $('#detailHeight').text(cattle.height_cm ? `${cattle.height_cm} cm` : '—');
    $('#detailEarTag').text(valueOrDash(cattle.ear_tag));
    $('#detailChip').text(valueOrDash(cattle.chip_number));
    $('#detailObservations').text(valueOrDash(cattle.observations));
    $('#detailCreatedAt').text(valueOrDash(cattle.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(cattle.updated_at_formatted));
    $('#detailFather').text(valueOrDash(cattle.father_label || 'No registrado'));
    $('#detailFatherBreed').text(cattle.father_breed_name ? `Raza: ${cattle.father_breed_name}` : 'No registrado');
    $('#detailMother').text(valueOrDash(cattle.mother_label || 'No registrado'));
    $('#detailMotherBreed').text(cattle.mother_breed_name ? `Raza: ${cattle.mother_breed_name}` : 'No registrado');
    setGenealogyShortcutLinks(cattle);
    setOwnershipHistoryShortcutLink(cattle);
    setCattleSaleShortcutLink(cattle);
    setCertificateShortcutLink(cattle);
    setVeterinaryRecordShortcutLink(cattle);
    setVaccinationShortcutLink(cattle);
    setTreatmentShortcutLink(cattle);
    setWeightRecordShortcutLink(cattle);
    setReproductionRecordShortcutLink(cattle);
    setCattleDetailPhoto(cattle.photo_url || null);
    renderOwnershipHistories(cattle.ownership_histories || []);
    renderCattleSales(cattle.sales || []);
    renderCertificates(cattle.certificates || []);
    renderVeterinaryRecords(cattle.veterinary_records || []);
    renderVaccinations(cattle.vaccinations || []);
    renderTreatments(cattle.treatments || []);
    renderWeightRecords(cattle.weight_records || [], cattle.latest_weight_record || null, cattle.previous_weight_record || null);
    renderReproductionRecords(cattle.reproduction_records || [], cattle.reproduction_as_partner || []);
    renderCattlePhotoGallery(cattle.photos || []);

    $('#detailSexBadge').html(cattle.sex === 'male'
        ? '<span class="badge badge-primary px-3 py-2">Macho</span>'
        : '<span class="badge badge-info px-3 py-2">Hembra</span>');
    $('#detailStatusBadge').html(statusBadge(cattle.status));
    $('#detailSaleStatusBadge').html(saleStatusBadge(cattle.sale_status));
    $('#detailPublicBadge').html(cattle.is_public
        ? '<span class="badge badge-success px-3 py-2">Público</span>'
        : '<span class="badge badge-secondary px-3 py-2">Privado</span>');
}

function renderCattlePhotoGallery(photos) {
    const $gallery = $('#cattlePhotoGallery');
    const $empty = $('#cattlePhotoGalleryEmpty');

    $gallery.empty();
    $empty.toggleClass('d-none', (photos || []).length > 0);

    (photos || []).forEach(function (photo) {
        $gallery.append(`
            <div class="cattle-gallery-item">
                <img src="${escapeHtml(photo.image_url || '')}" alt="${escapeHtml(photo.title || 'Foto del ganado')}">
                <div class="cattle-gallery-item-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <strong>${escapeHtml(photo.title || 'Foto del ganado')}</strong>
                        ${photo.is_main ? '<span class="badge badge-success">Principal</span>' : ''}
                    </div>
                    <div class="text-muted small">${escapeHtml(photo.description || '')}</div>
                    <div class="cattle-gallery-actions">
                        <button type="button" class="btn btn-outline-secondary btn-xs viewCattlePhoto" data-id="${photo.id}"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-outline-info btn-xs editCattlePhoto" data-id="${photo.id}"><i class="fas fa-pen"></i></button>
                        ${photo.is_main ? '' : `<button type="button" class="btn btn-outline-success btn-xs setMainCattlePhoto" data-id="${photo.id}"><i class="fas fa-star"></i></button>`}
                        <button type="button" class="btn btn-outline-danger btn-xs deleteCattlePhoto" data-id="${photo.id}"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        `);
    });
}

function renderOwnershipHistories(histories) {
    const $list = $('#detailOwnershipHistoryList');
    const $empty = $('#detailOwnershipHistoryEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (histories || []).length > 0);

    (histories || []).forEach(function (history) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${history.is_current ? 'Propietario actual' : 'Propietario anterior'}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(history.owner_name || '-')}</div>
                    </div>
                    ${history.is_current ? '<span class="badge badge-success">Actual</span>' : '<span class="badge badge-secondary">Historico</span>'}
                </div>
                <div class="text-muted small mt-2">
                    ${escapeHtml(history.start_date || '-')} - ${escapeHtml(history.end_date || '-')}
                    <span class="mx-1">|</span>
                    ${escapeHtml(history.acquisition_type_label || '-')}
                </div>
            </div>
        `);
    });
}

function renderCattleSales(sales) {
    const $list = $('#detailCattleSalesList');
    const $empty = $('#detailCattleSalesEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (sales || []).length > 0);

    (sales || []).forEach(function (sale) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">Venta</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(sale.buyer_name || '-')}</div>
                    </div>
                    ${cattleSaleStatusBadge(sale.status, sale.status_label)}
                </div>
                <div class="text-muted small mt-2">
                    ${escapeHtml(sale.sale_date || '-')} - ${escapeHtml(sale.sale_price || '-')}
                    <span class="mx-1">|</span>
                    ${escapeHtml(sale.payment_method_label || '-')}
                </div>
            </div>
        `);
    });
}

function renderCertificates(certificates) {
    const $list = $('#detailCertificatesList');
    const $empty = $('#detailCertificatesEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (certificates || []).length > 0);

    (certificates || []).forEach(function (certificate) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(certificate.issue_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(certificate.certificate_number || '-')}</div>
                    </div>
                    ${certificateStatusBadge(certificate.status, certificate.status_label)}
                </div>
                <div class="text-muted small mt-2">
                    Tipo: ${escapeHtml(certificate.certificate_type_label || '-')}
                </div>
                <div class="mt-2">
                    ${certificate.pdf_url ? `<a class="btn btn-outline-danger btn-xs mr-1" href="${escapeHtml(certificate.pdf_url)}" target="_blank" rel="noopener"><i class="fas fa-file-pdf mr-1"></i> Ver PDF</a>` : ''}
                    ${certificate.verify_url ? `<a class="btn btn-outline-success btn-xs" href="${escapeHtml(certificate.verify_url)}" target="_blank" rel="noopener"><i class="fas fa-check-circle mr-1"></i> Verificar</a>` : ''}
                </div>
            </div>
        `);
    });
}

function certificateStatusBadge(status, label) {
    if (status === 'issued') {
        return `<span class="badge badge-success">${escapeHtml(label || 'Emitido')}</span>`;
    }

    if (status === 'cancelled') {
        return `<span class="badge badge-danger">${escapeHtml(label || 'Anulado')}</span>`;
    }

    return `<span class="badge badge-secondary">${escapeHtml(label || 'Vencido')}</span>`;
}

function cattleSaleStatusBadge(status, label) {
    if (status === 'completed') {
        return `<span class="badge badge-success">${escapeHtml(label || 'Completado')}</span>`;
    }

    if (status === 'cancelled') {
        return `<span class="badge badge-danger">${escapeHtml(label || 'Anulado')}</span>`;
    }

    return `<span class="badge badge-warning">${escapeHtml(label || 'Pendiente')}</span>`;
}

function renderVeterinaryRecords(records) {
    const $list = $('#detailVeterinaryRecordsList');
    const $empty = $('#detailVeterinaryRecordsEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (records || []).length > 0);

    (records || []).forEach(function (record) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(record.record_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(record.record_type_label || '-')}</div>
                    </div>
                    ${veterinaryRecordTypeBadge(record.record_type, record.record_type_label)}
                </div>
                <div class="text-muted small mt-2">
                    ${escapeHtml(record.veterinarian_name || 'Sin veterinario')}
                    <span class="mx-1">|</span>
                    Proxima: ${escapeHtml(record.next_visit_date || 'Sin programar')}
                </div>
                <div class="text-muted small mt-1">${escapeHtml(shortText(record.diagnosis || '-', 90))}</div>
            </div>
        `);
    });
}

function veterinaryRecordTypeBadge(type, label) {
    if (type === 'emergency') {
        return `<span class="badge badge-danger">${escapeHtml(label || 'Emergencia')}</span>`;
    }

    if (type === 'illness') {
        return `<span class="badge badge-warning">${escapeHtml(label || 'Enfermedad')}</span>`;
    }

    if (type === 'control') {
        return `<span class="badge badge-primary">${escapeHtml(label || 'Control')}</span>`;
    }

    if (type === 'certification') {
        return `<span class="badge badge-info">${escapeHtml(label || 'Certificacion')}</span>`;
    }

    if (type === 'checkup') {
        return `<span class="badge badge-success">${escapeHtml(label || 'Revision')}</span>`;
    }

    return `<span class="badge badge-secondary">${escapeHtml(label || 'Otro')}</span>`;
}

function renderVaccinations(vaccinations) {
    const $list = $('#detailVaccinationsList');
    const $empty = $('#detailVaccinationsEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (vaccinations || []).length > 0);

    (vaccinations || []).forEach(function (vaccination) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(vaccination.application_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(vaccination.vaccine_name || '-')}</div>
                    </div>
                    ${vaccinationDueBadge(vaccination.next_due_status, vaccination.next_due_status_label)}
                </div>
                <div class="text-muted small mt-2">
                    Dosis: ${escapeHtml(vaccination.dose || 'No registrada')}
                    <span class="mx-1">|</span>
                    Lote: ${escapeHtml(vaccination.batch_number || 'No registrado')}
                </div>
                <div class="text-muted small mt-1">
                    Proxima: ${escapeHtml(vaccination.next_due_date || 'Sin programar')}
                    <span class="mx-1">|</span>
                    ${escapeHtml(vaccination.veterinarian_name || 'Sin veterinario')}
                </div>
            </div>
        `);
    });
}

function vaccinationDueBadge(status, label) {
    if (status === 'scheduled') {
        return `<span class="badge badge-info">${escapeHtml(label || 'Programada')}</span>`;
    }

    if (status === 'today') {
        return `<span class="badge badge-warning">${escapeHtml(label || 'Aplicar hoy')}</span>`;
    }

    if (status === 'overdue') {
        return `<span class="badge badge-danger">${escapeHtml(label || 'Vencida')}</span>`;
    }

    return `<span class="badge badge-secondary">${escapeHtml(label || 'Sin proxima dosis')}</span>`;
}

function renderTreatments(treatments) {
    const $list = $('#detailTreatmentsList');
    const $empty = $('#detailTreatmentsEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (treatments || []).length > 0);

    (treatments || []).forEach(function (treatment) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(treatment.treatment_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(treatment.treatment_name || '-')}</div>
                    </div>
                    ${treatment.duration ? `<span class="badge badge-warning">${escapeHtml(treatment.duration)}</span>` : '<span class="badge badge-success">Registrado</span>'}
                </div>
                <div class="text-muted small mt-2">
                    Medicamento: ${escapeHtml(treatment.medicine || 'No registrado')}
                    <span class="mx-1">|</span>
                    Dosis: ${escapeHtml(treatment.dose || 'No registrada')}
                </div>
                <div class="text-muted small mt-1">
                    ${escapeHtml(treatment.veterinarian_name || 'Sin veterinario')}
                </div>
            </div>
        `);
    });
}

function renderWeightRecords(records, latest, previous) {
    const $list = $('#detailWeightRecordsList');
    const $empty = $('#detailWeightRecordsEmpty');
    const $summary = $('#detailLatestWeightSummary');

    $list.empty();
    $empty.toggleClass('d-none', (records || []).length > 0);

    if (latest) {
        const evolution = previous
            ? ` ${weightEvolutionText(Number(previous.difference))}`
            : '';

        $summary
            .removeClass('d-none')
            .text(`Ultimo peso registrado: ${latest.weight_kg} (${latest.record_date || 'Sin fecha'}).${evolution}`);
    } else {
        $summary.addClass('d-none').text('');
    }

    (records || []).forEach(function (record) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(record.record_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(record.weight_kg || '-')}</div>
                    </div>
                    ${weightConditionBadge(record.body_condition)}
                </div>
                <div class="text-muted small mt-2">${escapeHtml(shortText(record.observations || 'Sin observaciones', 90))}</div>
            </div>
        `);
    });
}

function renderReproductionRecords(records, partnerRecords) {
    const $list = $('#detailReproductionRecordsList');
    const $empty = $('#detailReproductionRecordsEmpty');
    const $partnerList = $('#detailReproductionPartnerList');
    const $partnerEmpty = $('#detailReproductionPartnerEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (records || []).length > 0);

    (records || []).forEach(function (record) {
        $list.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(record.reproduction_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(record.method_label || '-')}</div>
                    </div>
                    ${pregnancyResultBadge(record.pregnancy_result, record.pregnancy_result_label)}
                </div>
                <div class="text-muted small mt-2">
                    Pareja: ${escapeHtml(record.partner_label || 'Sin pareja registrada')}
                    <span class="mx-1">|</span>
                    Parto: ${escapeHtml(record.birth_date || 'Sin parto')}
                </div>
                <div class="text-muted small mt-1">Cria: ${escapeHtml(record.offspring_label || 'Sin cria vinculada')}</div>
            </div>
        `);
    });

    $partnerList.empty();
    $partnerEmpty.toggleClass('d-none', (partnerRecords || []).length > 0);

    (partnerRecords || []).forEach(function (record) {
        $partnerList.append(`
            <div class="cattle-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="cattle-detail-label">${escapeHtml(record.reproduction_date || '-')}</div>
                        <div class="cattle-detail-value font-weight-bold">${escapeHtml(record.cattle_label || '-')}</div>
                    </div>
                    ${pregnancyResultBadge(record.pregnancy_result, record.pregnancy_result_label)}
                </div>
                <div class="text-muted small mt-2">
                    ${escapeHtml(record.method_label || '-')}
                    <span class="mx-1">|</span>
                    Cria: ${escapeHtml(record.offspring_label || 'Sin cria vinculada')}
                </div>
            </div>
        `);
    });
}

function weightEvolutionText(difference) {
    const abs = Math.abs(difference).toFixed(2);

    if (difference > 0) {
        return `Subio ${abs} kg desde el pesaje anterior.`;
    }

    if (difference < 0) {
        return `Bajo ${abs} kg desde el pesaje anterior.`;
    }

    return 'No tuvo variacion frente al pesaje anterior.';
}

function weightConditionBadge(condition) {
    const key = String(condition || '').toLowerCase();
    const classes = {
        excelente: 'badge-success',
        buena: 'badge-info',
        regular: 'badge-warning',
        baja: 'badge-orange',
        critica: 'badge-danger'
    };

    return `<span class="badge ${classes[key] || 'badge-secondary'}">${escapeHtml(condition || 'Sin dato')}</span>`;
}

function pregnancyResultBadge(result, label) {
    if (result === 'positive') {
        return `<span class="badge badge-success">${escapeHtml(label || 'Positivo')}</span>`;
    }

    if (result === 'negative') {
        return `<span class="badge badge-danger">${escapeHtml(label || 'Negativo')}</span>`;
    }

    return `<span class="badge badge-warning">${escapeHtml(label || 'Pendiente')}</span>`;
}

function shortText(value, maxLength) {
    const text = String(value || '');

    return text.length > maxLength ? `${text.slice(0, maxLength)}...` : text;
}

function openAddCattlePhotoModal() {
    resetCattlePhotoForm();
    $('#cattlePhotoFormTitle').text('Agregar foto');
    $('#cattlePhotoForm').removeAttr('data-id');
    $('#cattlePhotoFormModal').modal('show');
}

function editCattlePhoto() {
    const photoId = $(this).data('id');

    $.get(`${window.cattleRoutes.photoBase}/${photoId}`)
        .done(function (response) {
            const photo = response.photo;
            resetCattlePhotoForm();
            $('#cattlePhotoFormTitle').text('Editar foto');
            $('#cattlePhotoForm').attr('data-id', photo.id);
            $('#photo_title').val(photo.title || '');
            $('#photo_description').val(photo.description || '');
            $('#photo_sort_order').val(photo.sort_order || 0);
            $('#photo_is_main').prop('checked', Boolean(photo.is_main));
            $('#cattlePhotoFormModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la foto.', 'error');
        });
}

function viewCattlePhoto() {
    const photoId = $(this).data('id');

    $.get(`${window.cattleRoutes.photoBase}/${photoId}`)
        .done(function (response) {
            const photo = response.photo;
            $('#photoViewTitle').text(photo.title || 'Foto del ganado');
            $('#photoViewImage').attr('src', photo.image_url || '');
            $('#photoViewDescription').text(photo.description || '');
            $('#photoViewMainBadge').html(photo.is_main ? '<span class="badge badge-success mb-2">Principal</span>' : '');
            $('#cattlePhotoViewModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo abrir la foto.', 'error');
        });
}

function submitCattlePhotoForm(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const photoId = $('#cattlePhotoForm').attr('data-id');
    const url = photoId
        ? `${window.cattleRoutes.photoBase}/${photoId}`
        : `${window.cattleRoutes.photosBase}/${currentDetailCattleId}/photos`;

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#cattlePhotoFormModal').modal('hide');
            reloadCattlePhotos();
            tableCattle.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showPhotoValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la foto.', 'error');
        }
    });
}

function setMainCattlePhoto() {
    const photoId = $(this).data('id');

    $.post(`${window.cattleRoutes.photoBase}/${photoId}/main`)
        .done(function (response) {
            reloadCattlePhotos();
            tableCattle.ajax.reload(null, false);
            showToast(response.message);
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo marcar la foto principal.', 'error');
        });
}

function deleteCattlePhoto() {
    const photoId = $(this).data('id');

    Swal.fire({
        title: '¿Eliminar foto?',
        text: 'La foto se quitará de la galería.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: `${window.cattleRoutes.photoBase}/${photoId}`,
            type: 'DELETE',
            success: function (response) {
                reloadCattlePhotos();
                tableCattle.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la foto.', 'error');
            }
        });
    });
}

function reloadCattlePhotos() {
    if (!currentDetailCattleId) {
        return;
    }

    $.get(`${window.cattleRoutes.photosBase}/${currentDetailCattleId}/photos`)
        .done(function (response) {
            renderCattlePhotoGallery(response.photos || []);
            setCattleDetailPhoto(response.main_photo_url || null);
        });
}

function resetCattlePhotoForm() {
    document.getElementById('cattlePhotoForm').reset();
    $('#cattle-photo-error-messages').addClass('d-none').empty();
    $('#cattlePhotoForm .is-invalid').removeClass('is-invalid');
    $('#cattlePhotoForm .invalid-feedback').text('');
}

function showPhotoValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#cattle-photo-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos de la foto:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setGenealogyShortcutLinks(cattle) {
    const genealogyUrl = window.cattleRoutes.genealogy || '';
    const cattleId = cattle.id;

    $('#detailAddFatherLink')
        .attr('href', `${genealogyUrl}?cattle_id=${encodeURIComponent(cattleId)}&relation_type=father`)
        .toggleClass('d-none', Boolean(cattle.father_label));

    $('#detailAddMotherLink')
        .attr('href', `${genealogyUrl}?cattle_id=${encodeURIComponent(cattleId)}&relation_type=mother`)
        .toggleClass('d-none', Boolean(cattle.mother_label));
}

function setOwnershipHistoryShortcutLink(cattle) {
    const ownershipUrl = window.cattleRoutes.ownershipHistories || '';

    $('#detailAddOwnershipHistoryLink')
        .attr('href', `${ownershipUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setCattleSaleShortcutLink(cattle) {
    const salesUrl = window.cattleRoutes.sales || '';

    $('#detailAddCattleSaleLink')
        .attr('href', `${salesUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setCertificateShortcutLink(cattle) {
    const certificatesUrl = window.cattleRoutes.certificates || '';

    $('#detailAddCertificateLink')
        .attr('href', `${certificatesUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setVeterinaryRecordShortcutLink(cattle) {
    const veterinaryUrl = window.cattleRoutes.veterinaryRecords || '';

    $('#detailAddVeterinaryRecordLink')
        .attr('href', `${veterinaryUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setVaccinationShortcutLink(cattle) {
    const vaccinationUrl = window.cattleRoutes.vaccinations || '';

    $('#detailAddVaccinationLink')
        .attr('href', `${vaccinationUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setTreatmentShortcutLink(cattle) {
    const treatmentUrl = window.cattleRoutes.treatments || '';

    $('#detailAddTreatmentLink')
        .attr('href', `${treatmentUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setWeightRecordShortcutLink(cattle) {
    const weightRecordUrl = window.cattleRoutes.weightRecords || '';

    $('#detailAddWeightRecordLink')
        .attr('href', `${weightRecordUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setReproductionRecordShortcutLink(cattle) {
    const reproductionUrl = window.cattleRoutes.reproductionRecords || '';

    $('#detailAddReproductionRecordLink')
        .attr('href', `${reproductionUrl}?cattle_id=${encodeURIComponent(cattle.id || '')}`);
}

function setCattleDetailPhoto(url) {
    const $photo = $('#detailMainPhoto');
    const $placeholder = $('#detailMainPhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function statusBadge(status) {
    if (status === 'active') {
        return '<span class="badge badge-success px-3 py-2">Activo</span>';
    }

    if (status === 'dead') {
        return '<span class="badge badge-danger px-3 py-2">Fallecido</span>';
    }

    return '<span class="badge badge-warning px-3 py-2">Descartado</span>';
}

function saleStatusBadge(status) {
    if (status === 'available') {
        return '<span class="badge badge-success px-3 py-2">Disponible</span>';
    }

    if (status === 'reserved') {
        return '<span class="badge badge-warning px-3 py-2">Reservado</span>';
    }

    if (status === 'sold') {
        return '<span class="badge badge-info px-3 py-2">Vendido</span>';
    }

    return '<span class="badge badge-secondary px-3 py-2">No disponible</span>';
}

function resetCattleForm() {
    const form = document.getElementById('cattleForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#cattleForm').removeAttr('data-id');
    $('#code').val('');
    $('#status').val('active');
    $('#sale_status').val('not_available');
    $('#is_public').prop('checked', true);
    $('#cattleModalLabel').text('Nuevo Ganado');
    $('#saveCattleButton span').text('Guardar Ganado');
    currentCattlePhotoUrl = null;
    clearValidation();
    setCattlePhotoPreview(null);
    clearGalleryPhotoPreview();
}

function clearValidation() {
    $('#cattle-error-messages').addClass('d-none').empty();
    $('#cattleForm .is-invalid').removeClass('is-invalid');
    $('#cattleForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#cattle-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveCattleButton');

    $button.prop('disabled', isLoading);
    $button.find('i').toggleClass('fa-save', !isLoading).toggleClass('fa-spinner fa-spin', isLoading);
}

function showLoading() {
    if (divLoading) {
        divLoading.style.display = 'flex';
    }
}

function hideLoading() {
    if (divLoading) {
        divLoading.style.display = 'none';
    }
}

function showToast(message) {
    Swal.fire({
        icon: 'success',
        title: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

function valueOrDash(value) {
    return value || '—';
}

function escapeHtml(value) {
    return $('<div>').text(value).html();
}
