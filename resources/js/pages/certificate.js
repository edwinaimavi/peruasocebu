var divLoading = document.getElementById('divLoading');
let tableCertificate;
let certificateSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableCertificate = $('#tableCertificate').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.certificateRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'certificate_number', name: 'certificate_number', render: $.fn.dataTable.render.text() },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'certificate_type', name: 'certificate_type', orderable: false },
            { data: 'owner_name', name: 'owner.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'ranch_name', name: 'ranch.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'purity_percentage', name: 'purity_percentage', defaultContent: '-' },
            { data: 'issue_date', name: 'issue_date' },
            { data: 'status', name: 'status', orderable: false },
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

    $('#certificateForm').on('submit', submitCertificateForm);
    $('#cattle_id').on('change', fillCertificateFromCattle);

    $(document).on('click', '.editCertificate', editCertificate);
    $(document).on('click', '.viewCertificate', viewCertificate);
    $(document).on('click', '.deleteCertificate', deleteCertificate);
    $(document).on('click', '.cancelCertificate', cancelCertificate);
    $(document).on('click', '.regenerateCertificatePdf', regenerateCertificatePdf);
    $(document).on('click', '.deleteCertificateSignatureFromDetail', deleteCertificateSignatureFromDetail);

    $('#certificateModal').on('show.bs.modal', function () {
        if (!$('#certificateForm').attr('data-id')) {
            resetCertificateForm();
        }
    });

    $('#certificateModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#certificateModal').on('hidden.bs.modal', resetCertificateForm);
});

function submitCertificateForm(event) {
    event.preventDefault();

    if (certificateSubmitting) {
        return;
    }

    certificateSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const form = event.currentTarget;
    const certificateId = $('#certificateForm').attr('data-id');
    const formData = new FormData(form);
    let url = window.certificateRoutes.index;

    if (certificateId) {
        url = `${window.certificateRoutes.index}/${certificateId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#certificateModal').modal('hide');
            tableCertificate.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar el certificado. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            certificateSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editCertificate() {
    const certificateId = $(this).data('id');
    showLoading();

    $.get(`${window.certificateRoutes.index}/${certificateId}`)
        .done(function (response) {
            prepareEditForm(response.certificate);
            $('#certificateModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el certificado.', 'error');
        })
        .always(hideLoading);
}

function viewCertificate() {
    const certificateId = $(this).data('id');
    showLoading();

    $.get(`${window.certificateRoutes.index}/${certificateId}`)
        .done(function (response) {
            fillDetailModal(response.certificate);
            $('#certificateDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle del certificado.', 'error');
        })
        .always(hideLoading);
}

function deleteCertificate() {
    const certificateId = $(this).data('id');
    const name = $(this).data('name') || 'este certificado';

    Swal.fire({
        title: 'Eliminar certificado?',
        text: `Se eliminara "${name}". Para documentos emitidos se recomienda anular.`,
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
            url: `${window.certificateRoutes.index}/${certificateId}`,
            type: 'DELETE',
            success: function (response) {
                tableCertificate.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar el certificado.', 'error');
            },
            complete: hideLoading
        });
    });
}

function cancelCertificate() {
    const certificateId = $(this).data('id');
    const name = $(this).data('name') || 'este certificado';

    Swal.fire({
        title: 'Anular certificado?',
        text: `Se marcara "${name}" como anulado y se regenerara su PDF.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, anular',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#c69500'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        showLoading();

        $.post(`${window.certificateRoutes.index}/${certificateId}/cancel`)
            .done(function (response) {
                tableCertificate.ajax.reload(null, false);
                showToast(response.message);
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo anular el certificado.', 'error');
            })
            .always(hideLoading);
    });
}

function regenerateCertificatePdf() {
    const certificateId = $(this).data('id');
    showLoading();

    $.post(`${window.certificateRoutes.index}/${certificateId}/regenerate-pdf`)
        .done(function (response) {
            tableCertificate.ajax.reload(null, false);
            showToast(response.message);
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo regenerar el PDF.', 'error');
        })
        .always(hideLoading);
}

function fillCertificateFromCattle() {
    const cattleId = $('#cattle_id').val();

    if (!cattleId) {
        return;
    }

    $.get(`${window.certificateRoutes.cattleInfo}/${cattleId}`)
        .done(function (response) {
            const cattle = response.cattle || {};
            $('#ranch_id').val(cattle.ranch_id || '');
            $('#owner_id').val(cattle.owner_id || '');

            if (cattle.purity_percentage !== null && cattle.purity_percentage !== undefined) {
                $('#purity_percentage').val(cattle.purity_percentage);
            }
        });
}

function prepareEditForm(certificate) {
    resetCertificateForm();

    $('#certificateForm').attr('data-id', certificate.id);
    $('#certificateModalLabel').text('Editar Certificado');
    $('#saveCertificateButton span').text('Actualizar Certificado');
    $('#certificateReadonlyCard').removeClass('d-none');
    $('#readonly_certificate_number').val(certificate.certificate_number || '');
    $('#readonly_verification_code').val(certificate.verification_code || '');
    $('#readonly_pdf_link').toggleClass('d-none', !certificate.pdf_url).attr('href', certificate.pdf_url || '#');

    [
        'cattle_id', 'ranch_id', 'owner_id', 'veterinarian_id', 'issue_date',
        'purity_percentage', 'certificate_type', 'observations', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(certificate[field] ?? '');
    });
}

function fillDetailModal(certificate) {
    $('#detailCertificateSubtitle').text(`Registro #${certificate.id}`);
    $('#detailCertificateNumber').text(valueOrDash(certificate.certificate_number));
    $('#detailVerificationCode').text(valueOrDash(certificate.verification_code));
    $('#detailCertificateBadges').html(`${typeBadge(certificate.certificate_type, certificate.certificate_type_label)} ${statusBadge(certificate.status, certificate.status_label)}`);
    $('#detailType').html(typeBadge(certificate.certificate_type, certificate.certificate_type_label));
    $('#detailIssueDate').text(valueOrDash(certificate.issue_date_formatted));
    $('#detailStatus').html(statusBadge(certificate.status, certificate.status_label));
    $('#detailPurity').text(valueOrDash(certificate.purity_label));
    $('#detailCattleLabel').text(valueOrDash(certificate.cattle_label));
    $('#detailCattleBreed').text(certificate.cattle_breed_name ? `Raza: ${certificate.cattle_breed_name}` : '-');
    $('#detailCattleCode').text(valueOrDash(certificate.cattle_code));
    $('#detailCattleSex').text(valueOrDash(certificate.cattle_sex_label));
    $('#detailCattleBirthDate').text(valueOrDash(certificate.cattle_birth_date));
    $('#detailCattlePurity').text(valueOrDash(certificate.cattle_purity_label));
    $('#detailRanch').text(valueOrDash(certificate.ranch_name));
    $('#detailRanchData').text([certificate.ranch_document, certificate.ranch_address].filter(Boolean).join(' | '));
    $('#detailOwner').text(valueOrDash(certificate.owner_name));
    $('#detailOwnerData').text([certificate.owner_document, certificate.owner_phone, certificate.owner_email].filter(Boolean).join(' | '));
    $('#detailVeterinarian').text(valueOrDash(certificate.veterinarian_name));
    $('#detailVeterinarianData').text([certificate.veterinarian_license, certificate.veterinarian_specialty].filter(Boolean).join(' | '));
    $('#detailObservations').text(valueOrDash(certificate.observations));
    $('#detailCreatedAt').text(valueOrDash(certificate.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(certificate.updated_at_formatted));

    $('#detailPdfLink').toggleClass('d-none', !certificate.pdf_url).attr('href', certificate.pdf_url || '#');
    $('#detailVerifyLink').attr('href', certificate.verify_url || '#');
    $('#detailQrImage').toggleClass('d-none', !certificate.qr_url).attr('src', certificate.qr_url || '');
    $('#detailQrPending').toggleClass('d-none', Boolean(certificate.qr_url));
    $('#detailCattlePhoto').toggleClass('d-none', !certificate.cattle_photo_url).attr('src', certificate.cattle_photo_url || '');
    $('#detailAddCertificateSignatureLink')
        .attr('href', `${window.certificateRoutes.signatures}?certificate_id=${encodeURIComponent(certificate.id || '')}`);
    renderCertificateSignatures(certificate.signatures || []);
}

function renderCertificateSignatures(signatures) {
    const $list = $('#detailCertificateSignaturesList');
    const $empty = $('#detailCertificateSignaturesEmpty');

    $list.empty();
    $empty.toggleClass('d-none', (signatures || []).length > 0);

    (signatures || []).forEach(function (signature) {
        $list.append(`
            <div class="certificate-detail-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="certificate-detail-label">${escapeHtml(signature.person_type_label || '-')}</div>
                        <div class="certificate-detail-value font-weight-bold">${escapeHtml(signature.person_name || '-')}</div>
                        <div class="text-muted small">${escapeHtml(signature.position || 'Sin cargo registrado')}</div>
                    </div>
                    ${personTypeBadge(signature.person_type, signature.person_type_label)}
                </div>
                <div class="d-flex flex-wrap align-items-center mt-3" style="gap: 8px;">
                    ${signature.signature_url ? `<img class="certificate-signature-thumb" src="${escapeHtml(signature.signature_url)}" alt="Firma">` : '<span class="badge badge-secondary">Sin firma</span>'}
                    ${signature.seal_url ? `<img class="certificate-signature-thumb" src="${escapeHtml(signature.seal_url)}" alt="Sello">` : '<span class="badge badge-secondary">Sin sello</span>'}
                </div>
                <div class="mt-3">
                    <a class="btn btn-outline-info btn-xs mr-1" href="${window.certificateRoutes.signatures}?signature_id=${encodeURIComponent(signature.id)}">
                        <i class="fas fa-pen mr-1"></i> Editar
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-xs deleteCertificateSignatureFromDetail"
                        data-id="${escapeHtml(signature.id)}" data-name="${escapeHtml(signature.person_name || 'firma')}">
                        <i class="fas fa-trash mr-1"></i> Eliminar
                    </button>
                </div>
            </div>
        `);
    });
}

function deleteCertificateSignatureFromDetail() {
    const signatureId = $(this).data('id');
    const name = $(this).data('name') || 'esta firma';

    Swal.fire({
        title: 'Eliminar firma?',
        text: `Se eliminara "${name}" y se regenerara el PDF.`,
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
            url: `${window.certificateRoutes.signatures}/${signatureId}`,
            type: 'DELETE',
            success: function (response) {
                $('#certificateDetailModal').modal('hide');
                tableCertificate.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la firma.', 'error');
            },
            complete: hideLoading
        });
    });
}

function resetCertificateForm() {
    const form = document.getElementById('certificateForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#certificateForm').removeAttr('data-id');
    $('#certificateModalLabel').text('Nuevo Certificado');
    $('#saveCertificateButton span').text('Guardar Certificado');
    $('#issue_date').val(new Date().toISOString().slice(0, 10));
    $('#status').val('issued');
    $('#certificateReadonlyCard').addClass('d-none');
    $('#readonly_pdf_link').addClass('d-none').attr('href', '#');
    clearValidation();

    const params = new URLSearchParams(window.location.search);
    const cattleId = params.get('cattle_id');

    if (cattleId) {
        $('#cattle_id').val(cattleId).trigger('change');
    }
}

function clearValidation() {
    $('#certificate-error-messages').addClass('d-none').empty();
    $('#certificateForm .is-invalid').removeClass('is-invalid');
    $('#certificateForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#certificate-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function typeBadge(type, label) {
    const classes = {
        breed: 'badge-success',
        genealogy: 'badge-primary',
        ownership: 'badge-warning',
        purity: 'badge-info'
    };

    return `<span class="badge ${classes[type] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function statusBadge(status, label) {
    const classes = {
        issued: 'badge-success',
        cancelled: 'badge-danger',
        expired: 'badge-secondary'
    };

    return `<span class="badge ${classes[status] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function personTypeBadge(type, label) {
    const classes = {
        owner: 'badge-warning',
        veterinarian: 'badge-success',
        representative: 'badge-primary',
        certifier: 'badge-info',
        other: 'badge-secondary'
    };

    return `<span class="badge ${classes[type] || 'badge-secondary'}">${escapeHtml(label || '-')}</span>`;
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveCertificateButton');
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
