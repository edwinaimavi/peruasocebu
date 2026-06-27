var divLoading = document.getElementById('divLoading');
let tableVeterinarian;
let veterinarianSubmitting = false;
let documentLookupPending = false;
let currentSignatureUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableVeterinarian = $('#tableVeterinarian').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.veterinarianRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'document_number', name: 'document_number', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'full_name', name: 'full_name', render: $.fn.dataTable.render.text() },
            { data: 'license_number', name: 'license_number', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'specialty', name: 'specialty', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'phone', name: 'phone', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'email', name: 'email', defaultContent: '—', render: $.fn.dataTable.render.text() },
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

    $('#btnSearchDocument').on('click', consultDocument);
    $('#signature').on('change', function () {
        const file = this.files && this.files[0];

        if (!file) {
            setSignaturePreview(null);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (event) {
            setSignaturePreview(event.target.result, {
                fileName: file.name,
                removable: true
            });
        };
        reader.readAsDataURL(file);
    });
    $('#btnRemoveSignaturePreview').on('click', clearSelectedSignature);

    $('#veterinarianForm').on('submit', function (event) {
        event.preventDefault();

        if (veterinarianSubmitting) {
            return;
        }

        veterinarianSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const veterinarianId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.veterinarianRoutes.index;

        if (veterinarianId) {
            url = `${window.veterinarianRoutes.index}/${veterinarianId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#veterinarianModal').modal('hide');
                tableVeterinarian.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el veterinario. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                veterinarianSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editVeterinarian', function () {
        const veterinarianId = $(this).data('id');
        showLoading();

        $.get(`${window.veterinarianRoutes.index}/${veterinarianId}`)
            .done(function (response) {
                prepareEditForm(response.veterinarian);
                $('#veterinarianModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del veterinario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewVeterinarian', function () {
        const veterinarianId = $(this).data('id');
        showLoading();

        $.get(`${window.veterinarianRoutes.index}/${veterinarianId}`)
            .done(function (response) {
                fillDetailModal(response.veterinarian);
                $('#veterinarianDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del veterinario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteVeterinarian', function () {
        const veterinarianId = $(this).data('id');
        const veterinarianName = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar veterinario?',
            text: `Se eliminará "${veterinarianName}".`,
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
                url: `${window.veterinarianRoutes.index}/${veterinarianId}`,
                type: 'DELETE',
                success: function (response) {
                    tableVeterinarian.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el veterinario.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#veterinarianModal').on('show.bs.modal', function () {
        if (!$('#veterinarianForm').attr('data-id')) {
            resetVeterinarianForm();
        }
    });

    $('#veterinarianModal').on('shown.bs.modal', function () {
        $('#document_type').trigger('focus');
    });

    $('#veterinarianModal').on('hidden.bs.modal', resetVeterinarianForm);
});

function prepareEditForm(veterinarian) {
    resetVeterinarianForm();

    $('#veterinarianForm').attr('data-id', veterinarian.id);
    $('#veterinarianModalLabel').text('Editar Veterinario');
    $('#saveVeterinarianButton span').text('Actualizar Veterinario');

    [
        'full_name', 'document_type', 'document_number', 'license_number',
        'specialty', 'phone', 'email', 'address', 'notes', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(veterinarian[field] ?? '');
    });

    currentSignatureUrl = veterinarian.signature_url || null;
    setSignaturePreview(currentSignatureUrl);
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

    const url = window.veterinarianRoutes.consultDocument.replace(
        '__NUMBER__',
        encodeURIComponent(documentNumber)
    );

    $.get(url)
        .done(function (response) {
            Swal.close();
            fillDocumentData(response.type, response.data || {}, documentNumber);
            Swal.fire('Consulta completada', `${response.type} encontrado correctamente.`, 'success');
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
        return 'Seleccione el tipo de documento.';
    }

    if (!['DNI', 'RUC'].includes(documentType)) {
        return 'La búsqueda automática solo está disponible para DNI y RUC.';
    }

    if (!documentNumber) {
        return 'Ingrese el número de documento.';
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

    if (documentType === 'RUC' && !documentNumber.startsWith('10')) {
        return 'El RUC del veterinario debe ser de persona natural y empezar con 10.';
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
            $('#full_name').val(fullName.trim());
        }

        return;
    }

    const businessName = firstValue(data, ['razonSocial', 'razon_social']);
    const tradeName = firstValue(data, ['nombreComercial', 'nombre_comercial']);
    const address = firstValue(data, ['direccion']);

    if (businessName || tradeName) {
        $('#full_name').val(businessName || tradeName);
    }

    if (address) {
        $('#address').val(address);
    }
}

function setSignaturePreview(url, options = {}) {
    const $preview = $('#signaturePreview');
    const $placeholder = $('#signaturePlaceholder');
    const $fileName = $('#signatureFileName');
    const $removeButton = $('#btnRemoveSignaturePreview');

    if (!url) {
        $preview.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        $fileName.text('Ningún archivo seleccionado');
        $removeButton.addClass('d-none');
        return;
    }

    $preview.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
    $fileName.text(options.fileName || 'Firma actual');
    $removeButton.toggleClass('d-none', !options.removable);
}

function clearSelectedSignature() {
    $('#signature').val('');
    setSignaturePreview(currentSignatureUrl);
}

function setDetailSignature(url) {
    const $signature = $('#detailSignature');
    const $placeholder = $('#detailSignaturePlaceholder');

    if (!url) {
        $signature.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $signature.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function fillDetailModal(veterinarian) {
    $('#detailVeterinarianSubtitle').text(`Registro #${veterinarian.id}`);
    $('#detailFullName').text(valueOrDash(veterinarian.full_name));
    $('#detailProfessionalSummary').text(
        [veterinarian.license_number, veterinarian.specialty].filter(Boolean).join(' · ') || '—'
    );
    $('#detailDocumentType').text(valueOrDash(veterinarian.document_type_label));
    $('#detailDocumentNumber').text(valueOrDash(veterinarian.document_number));
    $('#detailLicenseNumber').text(valueOrDash(veterinarian.license_number));
    $('#detailSpecialty').text(valueOrDash(veterinarian.specialty));
    $('#detailPhone').text(valueOrDash(veterinarian.phone));
    $('#detailEmail').text(valueOrDash(veterinarian.email));
    $('#detailAddress').text(valueOrDash(veterinarian.address));
    $('#detailNotes').text(valueOrDash(veterinarian.notes));
    $('#detailCreatedAt').text(valueOrDash(veterinarian.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(veterinarian.updated_at_formatted));
    setDetailSignature(veterinarian.signature_url || null);
    $('#detailStatus').html(
        veterinarian.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );
}

function resetVeterinarianForm() {
    const form = document.getElementById('veterinarianForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#veterinarianForm').removeAttr('data-id');
    $('#status').val('active');
    $('#veterinarianModalLabel').text('Nuevo Veterinario');
    $('#saveVeterinarianButton span').text('Guardar Veterinario');
    currentSignatureUrl = null;
    clearValidation();
    setSignaturePreview(null);
}

function clearValidation() {
    $('#veterinarian-error-messages').addClass('d-none').empty();
    $('#veterinarianForm .is-invalid').removeClass('is-invalid');
    $('#veterinarianForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#veterinarian-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setDocumentLookupButtonLoading(isLoading) {
    const $button = $('#btnSearchDocument');

    $button.prop('disabled', isLoading);
    $button.find('i')
        .toggleClass('fa-search', !isLoading)
        .toggleClass('fa-spinner fa-spin', isLoading);
    $button.find('span').text(isLoading ? 'Buscando' : 'Buscar');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveVeterinarianButton');

    $button.prop('disabled', isLoading);
    $button.find('i').toggleClass('fa-save', !isLoading).toggleClass('fa-spinner fa-spin', isLoading);
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
