var divLoading = document.getElementById('divLoading');
let tableRanch;
let ranchSubmitting = false;
let documentLookupPending = false;
const previewObjectUrls = new Map();

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableRanch = $('#tableRanch').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.ranchRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name', render: $.fn.dataTable.render.text() },
            { data: 'business_name', name: 'business_name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'document_number', name: 'document_number', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'phone', name: 'phone', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'email', name: 'email', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'representative_name', name: 'representative_name', defaultContent: '—', render: $.fn.dataTable.render.text() },
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

    $('#ranchForm').on('submit', function (event) {
        event.preventDefault();

        if (ranchSubmitting) {
            return;
        }

        ranchSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const ranchId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.ranchRoutes.index;

        if (ranchId) {
            url = `${window.ranchRoutes.index}/${ranchId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#ranchModal').modal('hide');
                tableRanch.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el criadero. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                ranchSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $('#btnSearchDocument').on('click', consultDocument);

    $(document).on('click', '.editRanch', function () {
        const ranchId = $(this).data('id');
        showLoading();

        $.get(`${window.ranchRoutes.index}/${ranchId}`)
            .done(function (response) {
                prepareEditForm(response.ranch);
                $('#ranchModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del criadero.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewRanch', function () {
        const ranchId = $(this).data('id');
        showLoading();

        $.get(`${window.ranchRoutes.index}/${ranchId}`)
            .done(function (response) {
                fillDetailModal(response.ranch);
                $('#ranchDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del criadero.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteRanch', function () {
        const ranchId = $(this).data('id');
        const ranchName = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar criadero?',
            text: `Se eliminará "${ranchName}".`,
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
                url: `${window.ranchRoutes.index}/${ranchId}`,
                type: 'DELETE',
                success: function (response) {
                    tableRanch.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el criadero.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('.ranch-file-input').on('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            return;
        }

        setPreview(this.dataset.preview, this.dataset.placeholder, URL.createObjectURL(file), true);
    });

    $('#ranchModal').on('show.bs.modal', function () {
        if (!$('#ranchForm').attr('data-id')) {
            resetRanchForm();
        }
    });

    $('#ranchModal').on('hidden.bs.modal', resetRanchForm);
});

function prepareEditForm(ranch) {
    resetRanchForm();

    $('#ranchForm').attr('data-id', ranch.id);
    $('#ranchModalLabel').text('Editar Criadero / Hacienda');
    $('#saveRanchButton span').text('Actualizar Criadero');

    const fields = [
        'name', 'business_name', 'document_type', 'document_number', 'address',
        'department', 'province', 'district', 'phone', 'email',
        'representative_name', 'description', 'status'
    ];

    fields.forEach(function (field) {
        $(`#${field}`).val(ranch[field] ?? '');
    });

    setPreview('logoPreview', 'logoPlaceholder', ranch.logo_url);
    setPreview('sealPreview', 'sealPlaceholder', ranch.seal_url);
    setPreview('signaturePreview', 'signaturePlaceholder', ranch.signature_url);
}

function consultDocument() {
    if (documentLookupPending) {
        return;
    }

    const documentType = $('#document_type').val();
    const documentNumber = $('#document_number').val().trim();
    const validationMessage = validateDocumentLookup(documentType, documentNumber);

    if (validationMessage) {
        Swal.fire('Atención', validationMessage, 'warning');
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

    const url = window.ranchRoutes.consultDocument.replace(
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
                Swal.fire('Atención', responseMessage(xhr), 'warning');
                return;
            }

            if (xhr.status === 404) {
                Swal.fire('Documento no encontrado', 'Documento no encontrado o no válido.', 'warning');
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

function validateDocumentLookup(documentType, documentNumber) {
    if (!documentType) {
        return 'Seleccione el tipo de documento antes de buscar.';
    }

    if (!['DNI', 'RUC'].includes(documentType)) {
        return 'La consulta automática solo está disponible para DNI y RUC.';
    }

    if (!documentNumber) {
        return 'Ingrese el número de documento antes de buscar.';
    }

    if (!/^\d+$/.test(documentNumber)) {
        return 'El número de documento debe contener solo números.';
    }

    if (documentType === 'DNI' && documentNumber.length !== 8) {
        return 'El DNI debe tener 8 dígitos.';
    }

    if (documentType === 'RUC' && documentNumber.length !== 11) {
        return 'El RUC debe tener 11 dígitos.';
    }

    return null;
}

function fillDocumentData(type, data, documentNumber) {
    $('#document_type').val(type);
    $('#document_number').val(firstValue(data, ['numeroDocumento', 'numero_documento']) || documentNumber);

    if (type === 'DNI') {
        const fullName = firstValue(data, ['nombreCompleto', 'nombre_completo'])
            || [
                firstValue(data, ['nombres']),
                firstValue(data, ['apellidoPaterno', 'apellido_paterno']),
                firstValue(data, ['apellidoMaterno', 'apellido_materno'])
            ].filter(Boolean).join(' ');

        if (fullName) {
            $('#representative_name').val(fullName.trim());
        }

        return;
    }

    const businessName = firstValue(data, ['razonSocial', 'razon_social']);
    const tradeName = firstValue(data, ['nombreComercial', 'nombre_comercial']);
    const fieldMap = {
        business_name: businessName,
        representative_name: tradeName || businessName,
        address: firstValue(data, ['direccion']),
        department: firstValue(data, ['departamento']),
        province: firstValue(data, ['provincia']),
        district: firstValue(data, ['distrito'])
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

function responseMessage(xhr, fallback = 'Ocurrió un error al consultar el documento.') {
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

function fillDetailModal(ranch) {
    $('#detailRanchSubtitle').text(`Registro #${ranch.id}`);
    $('#detailName').text(valueOrDash(ranch.name));
    $('#detailBusinessName').text(valueOrDash(ranch.business_name));
    $('#detailDocument').text(valueOrDash(
        [ranch.document_type, ranch.document_number].filter(Boolean).join(' ')
    ));
    $('#detailAddress').text(valueOrDash(
        [ranch.address, ranch.district, ranch.province, ranch.department].filter(Boolean).join(', ')
    ));
    $('#detailPhone').text(valueOrDash(ranch.phone));
    $('#detailEmail').text(valueOrDash(ranch.email));
    $('#detailRepresentative').text(valueOrDash(ranch.representative_name));
    $('#detailDescription').text(valueOrDash(ranch.description));
    $('#detailCreatedAt').text(valueOrDash(ranch.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(ranch.updated_at_formatted));
    $('#detailStatus').html(
        ranch.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );

    setDetailMedia('detailLogo', 'detailLogoEmpty', ranch.logo_url);
    setDetailMedia('detailSeal', 'detailSealEmpty', ranch.seal_url);
    setDetailMedia('detailSignature', 'detailSignatureEmpty', ranch.signature_url);
}

function resetRanchForm() {
    const form = document.getElementById('ranchForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#ranchForm').removeAttr('data-id');
    $('#status').val('active');
    $('#ranchModalLabel').text('Nuevo Criadero / Hacienda');
    $('#saveRanchButton span').text('Guardar Criadero');
    clearValidation();

    ['logo', 'seal', 'signature'].forEach(function (file) {
        setPreview(`${file}Preview`, `${file}Placeholder`, null);
    });
}

function clearValidation() {
    $('#ranch-error-messages').addClass('d-none').empty();
    $('#ranchForm .is-invalid').removeClass('is-invalid');
    $('#ranchForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#ranch-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setPreview(previewId, placeholderId, url, isObjectUrl = false) {
    const $preview = $(`#${previewId}`);
    const $placeholder = $(`#${placeholderId}`);
    const oldObjectUrl = previewObjectUrls.get(previewId);

    if (oldObjectUrl) {
        URL.revokeObjectURL(oldObjectUrl);
        previewObjectUrls.delete(previewId);
    }

    if (!url) {
        $preview.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    if (isObjectUrl) {
        previewObjectUrls.set(previewId, url);
    }

    $preview.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setDetailMedia(imageId, emptyId, url) {
    const $image = $(`#${imageId}`);
    const $empty = $(`#${emptyId}`);

    if (url) {
        $image.attr('src', url).removeClass('d-none');
        $empty.addClass('d-none');
    } else {
        $image.attr('src', '').addClass('d-none');
        $empty.removeClass('d-none');
    }
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveRanchButton');

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
