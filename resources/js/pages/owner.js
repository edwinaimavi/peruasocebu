var divLoading = document.getElementById('divLoading');
let tableOwner;
let ownerSubmitting = false;
let documentLookupPending = false;
let ownerPhotoObjectUrl = null;
let currentOwnerPhotoUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableOwner = $('#tableOwner').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.ownerRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'owner_type', name: 'owner_type' },
            { data: 'document_number', name: 'document_number', defaultContent: '—' },
            { data: 'display_name', name: 'full_name' },
            { data: 'phone', name: 'phone', defaultContent: '—' },
            { data: 'email', name: 'email', defaultContent: '—' },
            { data: 'address', name: 'address', defaultContent: '—' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
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

    $('#owner_type').on('change', updateOwnerTypeFields);
    $('#btnSearchDocument').on('click', consultDocument);
    $('#photo').on('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            setOwnerPhotoPreview(null);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            setOwnerPhotoPreview(event.target.result, {
                fileName: file.name,
                removable: true
            });
        };
        reader.readAsDataURL(file);
    });
    $('#btnRemovePhotoPreview').on('click', clearSelectedOwnerPhoto);

    $('#ownerForm').on('submit', function (event) {
        event.preventDefault();

        if (ownerSubmitting) {
            return;
        }

        ownerSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const ownerId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.ownerRoutes.index;

        if (ownerId) {
            url = `${window.ownerRoutes.index}/${ownerId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#ownerModal').modal('hide');
                tableOwner.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el propietario. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                ownerSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editOwner', function () {
        const ownerId = $(this).data('id');
        showLoading();

        $.get(`${window.ownerRoutes.index}/${ownerId}`)
            .done(function (response) {
                prepareEditForm(response.owner);
                $('#ownerModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del propietario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewOwner', function () {
        const ownerId = $(this).data('id');
        showLoading();

        $.get(`${window.ownerRoutes.index}/${ownerId}`)
            .done(function (response) {
                fillDetailModal(response.owner);
                $('#ownerDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del propietario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteOwner', function () {
        const ownerId = $(this).data('id');
        const ownerName = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar propietario?',
            text: `Se eliminará "${ownerName}".`,
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
                url: `${window.ownerRoutes.index}/${ownerId}`,
                type: 'DELETE',
                success: function (response) {
                    tableOwner.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el propietario.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#ownerModal').on('show.bs.modal', function () {
        if (!$('#ownerForm').attr('data-id')) {
            resetOwnerForm();
        }
    });

    $('#ownerModal').on('shown.bs.modal', function () {
        $('#owner_type').trigger('focus');
    });

    $('#ownerModal').on('hidden.bs.modal', resetOwnerForm);
});

function prepareEditForm(owner) {
    resetOwnerForm();

    $('#ownerForm').attr('data-id', owner.id);
    $('#ownerModalLabel').text('Editar Propietario');
    $('#saveOwnerButton span').text('Actualizar Propietario');

    [
        'owner_type', 'document_type', 'document_number', 'full_name',
        'business_name', 'phone', 'email', 'address', 'notes', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(owner[field] ?? '');
    });

    updateOwnerTypeFields(false);
    currentOwnerPhotoUrl = owner.photo_url || null;
    setOwnerPhotoPreview(currentOwnerPhotoUrl);
}

function legacyConsultDocument() {
    if (documentLookupPending) {
        return;
    }

    const documentType = $('#document_type').val();
    const documentNumber = $('#document_number').val().trim();
    const validationMessage = validateDocumentLookup(documentType, documentNumber);

    if (validationMessage) {
        Swal.fire('AtenciÃ³n', validationMessage, 'warning');
        return;
    }

    documentLookupPending = true;
    setDocumentLookupButtonLoading(true);

    Swal.fire({
        title: 'Consultando documento...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: function () {
            Swal.showLoading();
        }
    });

    const url = window.ownerRoutes.consultDocument.replace(
        '__NUMBER__',
        encodeURIComponent(documentNumber)
    );

    $.get(url)
        .done(function (response) {
            Swal.close();
            fillDocumentData(response.type, response.data || {}, documentNumber);
            Swal.fire(
                'Consulta completada',
                `${response.type} encontrado correctamente.`,
                'success'
            );
        })
        .fail(function (xhr) {
            Swal.close();

            if (xhr.status === 422) {
                Swal.fire('AtenciÃ³n', responseMessage(xhr), 'warning');
                return;
            }

            if (xhr.status === 404) {
                Swal.fire('Documento no encontrado', 'Documento no encontrado o no vÃ¡lido.', 'warning');
                return;
            }

            Swal.fire(
                'Error',
                responseMessage(
                    xhr,
                    'No se pudo conectar con el servicio de consulta. Intente nuevamente.'
                ),
                'error'
            );
        })
        .always(function () {
            documentLookupPending = false;
            setDocumentLookupButtonLoading(false);
        });
}

function legacyValidateDocumentLookup(documentType, documentNumber) {
    if (!documentType) {
        return 'Seleccione el tipo de documento antes de buscar.';
    }

    if (!['DNI', 'RUC'].includes(documentType)) {
        return 'La bÃºsqueda automÃ¡tica solo estÃ¡ disponible para DNI y RUC.';
    }

    if (!documentNumber) {
        return 'Ingrese el nÃºmero de documento.';
    }

    if (!/^\d+$/.test(documentNumber)) {
        return 'El nÃºmero de documento debe contener solo nÃºmeros.';
    }

    if (documentType === 'DNI' && documentNumber.length !== 8) {
        return 'El DNI debe tener 8 dÃ­gitos.';
    }

    if (documentType === 'RUC' && documentNumber.length !== 11) {
        return 'El RUC debe tener 11 dÃ­gitos.';
    }

    return null;
}

function legacyFillDocumentData(type, data, documentNumber) {
    $('#document_type').val(type);
    $('#document_number').val(firstValue(data, ['numeroDocumento', 'numero_documento']) || documentNumber);

    if (type === 'DNI') {
        $('#owner_type').val('person').trigger('change');

        const fullName = firstValue(data, ['nombreCompleto', 'nombre_completo'])
            || [
                firstValue(data, ['nombres']),
                firstValue(data, ['apellidoPaterno', 'apellido_paterno']),
                firstValue(data, ['apellidoMaterno', 'apellido_materno'])
            ].filter(Boolean).join(' ');

        if (fullName) {
            $('#full_name').val(fullName.trim());
        }

        return;
    }

    $('#owner_type').val('company').trigger('change');

    const businessName = firstValue(data, ['razonSocial', 'razon_social']);
    const tradeName = firstValue(data, ['nombreComercial', 'nombre_comercial']);
    const fieldMap = {
        business_name: businessName,
        full_name: tradeName || businessName,
        address: firstValue(data, ['direccion'])
    };

    Object.entries(fieldMap).forEach(function ([field, value]) {
        if (value) {
            $(`#${field}`).val(value);
        }
    });
}

function firstValue(data, keys) {
    for (const key of keys) {
        if (data[key] !== undefined && data[key] !== null && String(data[key]).trim() !== '') {
            return String(data[key]).trim();
        }
    }

    return '';
}

function responseMessage(xhr, fallback = 'OcurriÃ³ un error al consultar el documento.') {
    return xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : fallback;
}

function consultDocument() {
    if (documentLookupPending) {
        return;
    }

    const ownerType = $('#owner_type').val();
    const documentType = $('#document_type').val();
    const documentNumber = $('#document_number').val().trim();
    const validationMessage = validateDocumentLookup(ownerType, documentType, documentNumber);

    if (validationMessage) {
        Swal.fire('Atenci\u00f3n', validationMessage, 'warning');
        return;
    }

    documentLookupPending = true;
    setDocumentLookupButtonLoading(true);

    Swal.fire({
        title: 'Consultando documento...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: function () {
            Swal.showLoading();
        }
    });

    const url = window.ownerRoutes.consultDocument.replace(
        '__NUMBER__',
        encodeURIComponent(documentNumber)
    );

    $.get(url)
        .done(function (response) {
            Swal.close();
            fillDocumentData(ownerType, response.type, response.data || {}, documentNumber);
            Swal.fire('Consulta completada', successDocumentMessage(ownerType, response.type), 'success');
        })
        .fail(function (xhr) {
            Swal.close();

            if (xhr.status === 422) {
                Swal.fire('Atenci\u00f3n', responseMessageFixed(xhr), 'warning');
                return;
            }

            if (xhr.status === 404) {
                Swal.fire('Documento no encontrado', 'Documento no encontrado o no v\u00e1lido.', 'warning');
                return;
            }

            Swal.fire(
                'Error',
                responseMessageFixed(
                    xhr,
                    'No se pudo conectar con el servicio de consulta. Intente nuevamente.'
                ),
                'error'
            );
        })
        .always(function () {
            documentLookupPending = false;
            setDocumentLookupButtonLoading(false);
        });
}

function validateDocumentLookup(ownerType, documentType, documentNumber) {
    if (!ownerType) {
        return 'Seleccione el tipo de propietario.';
    }

    if (!documentType) {
        return 'Seleccione el tipo de documento.';
    }

    if (!['DNI', 'RUC'].includes(documentType)) {
        return 'La b\u00fasqueda autom\u00e1tica solo est\u00e1 disponible para DNI y RUC.';
    }

    if (!documentNumber) {
        return 'Ingrese el n\u00famero de documento.';
    }

    if (!/^\d+$/.test(documentNumber)) {
        return 'El n\u00famero de documento debe contener solo n\u00fameros.';
    }

    if (documentType === 'DNI' && documentNumber.length !== 8) {
        return 'El DNI debe tener 8 d\u00edgitos.';
    }

    if (documentType === 'RUC' && documentNumber.length !== 11) {
        return 'El RUC debe tener 11 d\u00edgitos.';
    }

    if (ownerType === 'company' && documentType !== 'RUC') {
        return 'Las empresas solo pueden consultar por RUC.';
    }

    if (ownerType === 'company' && !documentNumber.startsWith('20')) {
        return 'El RUC de una empresa debe empezar con 20.';
    }

    if (ownerType === 'person' && documentType === 'RUC' && !documentNumber.startsWith('10')) {
        return 'El RUC de una persona natural debe empezar con 10.';
    }

    return null;
}

function fillDocumentData(ownerType, type, data, documentNumber) {
    $('#document_type').val(type);
    $('#document_number').val(firstValue(data, ['numeroDocumento', 'numero_documento']) || documentNumber);

    if (type === 'DNI') {
        $('#owner_type').val('person');
        updateOwnerTypeFields(false);

        const fullName = firstValue(data, ['nombreCompleto', 'nombre_completo'])
            || [
                firstValue(data, ['nombres']),
                firstValue(data, ['apellidoPaterno', 'apellido_paterno']),
                firstValue(data, ['apellidoMaterno', 'apellido_materno'])
            ].filter(Boolean).join(' ');

        if (fullName) {
            $('#full_name').val(fullName.trim());
        }

        return;
    }

    $('#owner_type').val(ownerType);
    updateOwnerTypeFields(false);

    const businessName = firstValue(data, ['razonSocial', 'razon_social']);
    const tradeName = firstValue(data, ['nombreComercial', 'nombre_comercial']);
    const fieldMap = {
        business_name: businessName,
        full_name: ownerType === 'company' ? tradeName || businessName : businessName || tradeName,
        address: firstValue(data, ['direccion'])
    };

    Object.entries(fieldMap).forEach(function ([field, value]) {
        if (value) {
            $(`#${field}`).val(value);
        }
    });
}

function successDocumentMessage(ownerType, documentType) {
    if (documentType === 'DNI') {
        return 'DNI encontrado correctamente.';
    }

    return ownerType === 'company'
        ? 'RUC de empresa encontrado correctamente.'
        : 'RUC de persona natural encontrado correctamente.';
}

function responseMessageFixed(xhr, fallback = 'Ocurri\u00f3 un error al consultar el documento.') {
    return xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : fallback;
}

function setDocumentLookupButtonLoading(isLoading) {
    const $button = $('#btnSearchDocument');

    $button.prop('disabled', isLoading);
    $button.find('i')
        .toggleClass('fa-search', !isLoading)
        .toggleClass('fa-spinner fa-spin', isLoading);
    $button.find('span').text(isLoading ? 'Buscando' : 'Buscar');
}

function setOwnerPhotoPreview(url, options = {}) {
    if (ownerPhotoObjectUrl) {
        URL.revokeObjectURL(ownerPhotoObjectUrl);
        ownerPhotoObjectUrl = null;
    }

    if (options.isObjectUrl) {
        ownerPhotoObjectUrl = url;
    }

    const $preview = $('#photoPreview');
    const $placeholder = $('#photoPlaceholder');
    const $fileName = $('#photoFileName');
    const $removeButton = $('#btnRemovePhotoPreview');

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

function clearSelectedOwnerPhoto() {
    $('#photo').val('');
    setOwnerPhotoPreview(currentOwnerPhotoUrl);
}

function setOwnerDetailPhoto(url) {
    const $photo = $('#detailPhoto');
    const $placeholder = $('#detailPhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function fillDetailModal(owner) {
    const isCompany = owner.owner_type === 'company';
    const displayName = isCompany && owner.business_name ? owner.business_name : owner.full_name;

    $('#detailOwnerSubtitle').text(`Registro #${owner.id}`);
    $('#detailDisplayName').text(valueOrDash(displayName));
    $('#detailContactName').text(
        isCompany ? `Contacto: ${valueOrDash(owner.full_name)}` : valueOrDash(owner.business_name)
    );
    $('#detailDocumentType').text(valueOrDash(owner.document_type_label));
    $('#detailDocumentNumber').text(valueOrDash(owner.document_number));
    $('#detailPhone').text(valueOrDash(owner.phone));
    $('#detailEmail').text(valueOrDash(owner.email));
    $('#detailAddress').text(valueOrDash(owner.address));
    $('#detailNotes').text(valueOrDash(owner.notes));
    $('#detailCreatedAt').text(valueOrDash(owner.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(owner.updated_at_formatted));
    setOwnerDetailPhoto(owner.photo_url || null);
    $('#detailOwnerType').html(
        isCompany
            ? '<span class="badge badge-info px-3 py-2">Empresa</span>'
            : '<span class="badge badge-primary px-3 py-2">Persona</span>'
    );
    $('#detailStatus').html(
        owner.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );
}

function resetOwnerForm() {
    const form = document.getElementById('ownerForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#ownerForm').removeAttr('data-id');
    $('#owner_type').val('person');
    $('#status').val('active');
    $('#ownerModalLabel').text('Nuevo Propietario');
    $('#saveOwnerButton span').text('Guardar Propietario');
    currentOwnerPhotoUrl = null;
    clearValidation();
    updateOwnerTypeFields();
    setOwnerPhotoPreview(null);
}

function legacyUpdateOwnerTypeFields() {
    const isCompany = $('#owner_type').val() === 'company';

    $('#businessNameGroup').toggleClass('d-none', !isCompany);
    $('#fullNameLabel').text(isCompany ? 'Representante o contacto' : 'Nombre completo');
    $('#fullNameHelp').toggleClass('d-none', !isCompany);
}

function updateOwnerTypeFields(forceResetDocument) {
    const isCompany = $('#owner_type').val() === 'company';
    const shouldResetDocument = forceResetDocument === true
        || (forceResetDocument && forceResetDocument.type === 'change');
    const $documentType = $('#document_type');

    $('#businessNameGroup').toggleClass('d-none', !isCompany);
    $('#fullNameLabel').text(isCompany ? 'Representante o contacto' : 'Nombre completo');
    $('#fullNameHelp').toggleClass('d-none', !isCompany);

    if (isCompany) {
        $documentType.find('option').each(function () {
            const isRuc = this.value === 'RUC';
            $(this).prop('disabled', !isRuc).toggle(isRuc);
        });
        $documentType.val('RUC');
    } else {
        $documentType.find('option').prop('disabled', false).show();

        if (shouldResetDocument || !$documentType.val()) {
            $documentType.val('DNI');
        }
    }

    if (shouldResetDocument) {
        $('#document_number').val('');
    }
}

function clearValidation() {
    $('#owner-error-messages').addClass('d-none').empty();
    $('#ownerForm .is-invalid').removeClass('is-invalid');
    $('#ownerForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#owner-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveOwnerButton');

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
