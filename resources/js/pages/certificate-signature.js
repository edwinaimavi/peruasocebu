var divLoading = document.getElementById('divLoading');
let tableCertificateSignature;
let certificateSignatureSubmitting = false;
let signatureObjectUrl = null;
let sealObjectUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableCertificateSignature = $('#tableCertificateSignature').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.certificateSignatureRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'certificate_label', name: 'certificate.certificate_number', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'person_type', name: 'person_type', orderable: false },
            { data: 'person_name', name: 'person_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'position', name: 'position', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'signature_badge', name: 'signature_path', orderable: false, searchable: false },
            { data: 'seal_badge', name: 'seal_path', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        order: [[1, 'desc']],
        language: { url: '/vendor/datatables/js/i18n/es-ES.json' },
        dom: `
            <'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
            <'row'<'col-sm-12'tr>>
            <'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-center justify-content-md-end'p>>
            <'row mt-3'<'col-sm-12 text-center'B>>
        `,
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fas fa-file-excel"></i> Excel', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', className: 'btn btn-danger btn-sm', text: '<i class="fas fa-file-pdf"></i> PDF', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="fas fa-print"></i> Imprimir', exportOptions: { columns: ':not(:last-child)' } }
        ],
        preDrawCallback: showLoading,
        drawCallback: hideLoading
    });

    $('#certificateSignatureForm').on('submit', submitCertificateSignatureForm);
    $('#signature_file').on('change', function () {
        previewSelectedFile(this, 'signature');
    });
    $('#seal_file').on('change', function () {
        previewSelectedFile(this, 'seal');
    });

    $(document).on('click', '.editCertificateSignature', editCertificateSignature);
    $(document).on('click', '.viewCertificateSignature', viewCertificateSignature);
    $(document).on('click', '.deleteCertificateSignature', deleteCertificateSignature);

    $('#certificateSignatureModal').on('show.bs.modal', function () {
        if (!$('#certificateSignatureForm').attr('data-id')) {
            resetCertificateSignatureForm();
        }
    });

    $('#certificateSignatureModal').on('shown.bs.modal', function () {
        $('#certificate_id').trigger('focus');
    });

    $('#certificateSignatureModal').on('hidden.bs.modal', resetCertificateSignatureForm);

    openFromUrlParams();
});

function submitCertificateSignatureForm(event) {
    event.preventDefault();

    if (certificateSignatureSubmitting) {
        return;
    }

    certificateSignatureSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const signatureId = $('#certificateSignatureForm').attr('data-id');
    const formData = new FormData(document.getElementById('certificateSignatureForm'));
    let url = window.certificateSignatureRoutes.index;

    if (signatureId) {
        url = `${window.certificateSignatureRoutes.index}/${signatureId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#certificateSignatureModal').modal('hide');
            tableCertificateSignature.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la firma. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            certificateSignatureSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editCertificateSignature() {
    const signatureId = $(this).data('id');
    openEditSignature(signatureId);
}

function openEditSignature(signatureId) {
    showLoading();

    $.get(`${window.certificateSignatureRoutes.index}/${signatureId}`)
        .done(function (response) {
            prepareEditForm(response.signature);
            $('#certificateSignatureModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la firma.', 'error');
        })
        .always(hideLoading);
}

function viewCertificateSignature() {
    const signatureId = $(this).data('id');
    showLoading();

    $.get(`${window.certificateSignatureRoutes.index}/${signatureId}`)
        .done(function (response) {
            fillDetailModal(response.signature);
            $('#certificateSignatureDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle de la firma.', 'error');
        })
        .always(hideLoading);
}

function deleteCertificateSignature() {
    const signatureId = $(this).data('id');
    const name = $(this).data('name') || 'esta firma';

    Swal.fire({
        title: 'Eliminar firma?',
        text: `Se eliminara "${name}" y se regenerara el PDF del certificado.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        showLoading();

        $.ajax({
            url: `${window.certificateSignatureRoutes.index}/${signatureId}`,
            type: 'DELETE',
            success: function (response) {
                tableCertificateSignature.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la firma.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(signature) {
    resetCertificateSignatureForm();

    $('#certificateSignatureForm').attr('data-id', signature.id);
    $('#certificateSignatureModalLabel').text('Editar Firma');
    $('#saveCertificateSignatureButton span').text('Actualizar Firma');

    ['certificate_id', 'person_type', 'person_name', 'position'].forEach(function (field) {
        $(`#${field}`).val(signature[field] ?? '');
    });

    setPreview('signature', signature.signature_url, 'Firma actual');
    setPreview('seal', signature.seal_url, 'Sello actual');
}

function fillDetailModal(signature) {
    $('#detailSignatureSubtitle').text(`Registro #${signature.id}`);
    $('#detailSignatureCertificateNumber').text(valueOrDash(signature.certificate_number));
    $('#detailSignatureVerificationCode').text(valueOrDash(signature.verification_code));
    $('#detailSignatureCattle').text(valueOrDash(signature.cattle_label));
    $('#detailSignatureCertificateMeta').html(`${escapeHtml(signature.certificate_type_label || '-')} ${certificateStatusBadge(signature.certificate_status_label)}`);
    $('#detailSignaturePersonType').html(personTypeBadge(signature.person_type, signature.person_type_label));
    $('#detailSignaturePersonName').text(valueOrDash(signature.person_name));
    $('#detailSignaturePosition').text(valueOrDash(signature.position));
    $('#detailSignatureCreatedAt').text(valueOrDash(signature.created_at_formatted));
    $('#detailSignatureUpdatedAt').text(valueOrDash(signature.updated_at_formatted));
    $('#detailSignatureImageWrap').html(signature.signature_url
        ? `<img class="signature-detail-image" src="${escapeHtml(signature.signature_url)}" alt="Firma">`
        : '<span class="text-muted">Sin firma registrada</span>');
    $('#detailSealImageWrap').html(signature.seal_url
        ? `<img class="signature-detail-image" src="${escapeHtml(signature.seal_url)}" alt="Sello">`
        : '<span class="text-muted">Sin sello registrado</span>');
}

function resetCertificateSignatureForm() {
    const form = document.getElementById('certificateSignatureForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#certificateSignatureForm').removeAttr('data-id');
    $('#certificateSignatureModalLabel').text('Nueva Firma');
    $('#saveCertificateSignatureButton span').text('Guardar Firma');
    clearFileInput('signature');
    clearFileInput('seal');
    clearValidation();

    const params = new URLSearchParams(window.location.search);
    const certificateId = params.get('certificate_id');

    if (certificateId) {
        $('#certificate_id').val(certificateId);
    }
}

function previewSelectedFile(input, type) {
    const file = input.files && input.files[0];

    if (!file) {
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    setPreview(type, objectUrl, file.name, true);
    $(`#${type}_file-error`).text('');
}

function setPreview(type, url, fileName, isObjectUrl = false) {
    if (type === 'signature' && signatureObjectUrl) {
        URL.revokeObjectURL(signatureObjectUrl);
        signatureObjectUrl = null;
    }

    if (type === 'seal' && sealObjectUrl) {
        URL.revokeObjectURL(sealObjectUrl);
        sealObjectUrl = null;
    }

    if (isObjectUrl && type === 'signature') {
        signatureObjectUrl = url;
    }

    if (isObjectUrl && type === 'seal') {
        sealObjectUrl = url;
    }

    const previewId = type === 'signature' ? '#signaturePreview' : '#sealPreview';
    const fileNameId = type === 'signature' ? '#signatureFileName' : '#sealFileName';
    const emptyText = type === 'signature' ? 'Sin firma registrada' : 'Sin sello registrado';

    if (!url) {
        $(previewId).html(`<span class="text-muted small">${emptyText}</span>`);
        $(fileNameId).text('Ningun archivo seleccionado');
        return;
    }

    $(previewId).html(`<img src="${escapeHtml(url)}" alt="${escapeHtml(fileName || emptyText)}">`);
    $(fileNameId).text(fileName || 'Archivo actual');
}

function clearFileInput(type) {
    const inputId = type === 'signature' ? '#signature_file' : '#seal_file';
    $(inputId).val('');
    setPreview(type, null);
}

function openFromUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const signatureId = params.get('signature_id');
    const certificateId = params.get('certificate_id');

    if (signatureId) {
        openEditSignature(signatureId);
        return;
    }

    if (certificateId) {
        resetCertificateSignatureForm();
        $('#certificate_id').val(certificateId);
        $('#certificateSignatureModal').modal('show');
    }
}

function clearValidation() {
    $('#certificate-signature-error-messages').addClass('d-none').empty();
    $('#certificateSignatureForm .is-invalid').removeClass('is-invalid');
    $('#certificateSignatureForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#certificate-signature-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function personTypeBadge(type, label) {
    const classes = {
        owner: 'badge-warning',
        veterinarian: 'badge-success',
        representative: 'badge-primary',
        certifier: 'badge-info',
        other: 'badge-secondary'
    };

    return `<span class="badge ${classes[type] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function certificateStatusBadge(label) {
    const normalized = String(label || '').toLowerCase();
    const badgeClass = normalized === 'emitido'
        ? 'badge-success'
        : (normalized === 'anulado' ? 'badge-danger' : 'badge-secondary');

    return `<span class="badge ${badgeClass}">${escapeHtml(label || '-')}</span>`;
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveCertificateSignatureButton');
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
    return value || '-';
}

function escapeHtml(value) {
    return $('<div>').text(value).html();
}
