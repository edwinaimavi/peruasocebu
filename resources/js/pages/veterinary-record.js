var divLoading = document.getElementById('divLoading');
let tableVeterinaryRecord;
let veterinaryRecordSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableVeterinaryRecord = $('#tableVeterinaryRecord').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.veterinaryRecordRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'veterinarian_name', name: 'veterinarian.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'record_type', name: 'record_type', orderable: false },
            { data: 'record_date', name: 'record_date' },
            { data: 'next_visit_date', name: 'next_visit_date' },
            { data: 'document', name: 'document_path', orderable: false, searchable: false },
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

    $('#document_file').on('change', function () {
        const file = this.files && this.files[0];
        $('#documentFileName').text(file ? file.name : 'Ningun archivo seleccionado');
        if (file) {
            $('#document_file-error').text('');
        }
    });

    $('#veterinaryRecordForm').on('submit', submitVeterinaryRecordForm);
    $(document).on('click', '.editVeterinaryRecord', editVeterinaryRecord);
    $(document).on('click', '.viewVeterinaryRecord', viewVeterinaryRecord);
    $(document).on('click', '.deleteVeterinaryRecord', deleteVeterinaryRecord);

    $('#veterinaryRecordModal').on('show.bs.modal', function () {
        if (!$('#veterinaryRecordForm').attr('data-id')) {
            resetVeterinaryRecordForm();
        }
    });

    $('#veterinaryRecordModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#veterinaryRecordModal').on('hidden.bs.modal', resetVeterinaryRecordForm);
});

function submitVeterinaryRecordForm(event) {
    event.preventDefault();

    if (veterinaryRecordSubmitting) {
        return;
    }

    veterinaryRecordSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const form = event.currentTarget;
    const recordId = $('#veterinaryRecordForm').attr('data-id');
    const formData = new FormData(form);
    let url = window.veterinaryRecordRoutes.index;

    if (recordId) {
        url = `${window.veterinaryRecordRoutes.index}/${recordId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#veterinaryRecordModal').modal('hide');
            tableVeterinaryRecord.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la revision. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            veterinaryRecordSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editVeterinaryRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.veterinaryRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            prepareEditForm(response.record);
            $('#veterinaryRecordModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la revision.', 'error');
        })
        .always(hideLoading);
}

function viewVeterinaryRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.veterinaryRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            fillDetailModal(response.record);
            $('#veterinaryRecordDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle de la revision.', 'error');
        })
        .always(hideLoading);
}

function deleteVeterinaryRecord() {
    const recordId = $(this).data('id');
    const recordName = $(this).data('name') || 'esta revision';

    Swal.fire({
        title: 'Eliminar revision?',
        text: `Se eliminara "${recordName}".`,
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
            url: `${window.veterinaryRecordRoutes.index}/${recordId}`,
            type: 'DELETE',
            success: function (response) {
                tableVeterinaryRecord.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la revision.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(record) {
    resetVeterinaryRecordForm();

    $('#veterinaryRecordForm').attr('data-id', record.id);
    $('#veterinaryRecordModalLabel').text('Editar Revision');
    $('#saveVeterinaryRecordButton span').text('Actualizar Revision');

    [
        'cattle_id', 'veterinarian_id', 'record_date', 'record_type',
        'diagnosis', 'treatment', 'observations', 'next_visit_date'
    ].forEach(function (field) {
        $(`#${field}`).val(record[field] ?? '');
    });

    $('#currentDocumentLink')
        .toggleClass('d-none', !record.document_url)
        .attr('href', record.document_url || '#');
    $('#documentFileName').text(record.document_name || 'Ningun archivo seleccionado');
}

function fillDetailModal(record) {
    $('#detailVeterinarySubtitle').text(`Registro #${record.id}`);
    $('#detailVeterinaryCattle').text(valueOrDash(record.cattle_label));
    $('#detailVeterinaryBreed').text(record.cattle_breed_name ? `Raza: ${record.cattle_breed_name}` : '-');
    $('#detailVeterinaryOwner').text(record.cattle_owner_name ? `Propietario: ${record.cattle_owner_name}` : 'Propietario no registrado');
    $('#detailVeterinarian').text(valueOrDash(record.veterinarian_name));
    $('#detailVeterinarianLicense').text(valueOrDash(record.veterinarian_license));
    $('#detailVeterinarianSpecialty').text(valueOrDash(record.veterinarian_specialty));
    $('#detailRecordDate').text(valueOrDash(record.record_date_formatted));
    $('#detailNextVisitDate').text(valueOrDash(record.next_visit_date_formatted));
    $('#detailDiagnosis').text(valueOrDash(record.diagnosis));
    $('#detailTreatment').text(valueOrDash(record.treatment));
    $('#detailObservations').text(valueOrDash(record.observations));
    $('#detailVeterinaryCreatedAt').text(valueOrDash(record.created_at_formatted));
    $('#detailVeterinaryUpdatedAt').text(valueOrDash(record.updated_at_formatted));
    $('#detailRecordTypeBadge').html(recordTypeBadge(record.record_type, record.record_type_label));
    $('#detailVeterinaryDocument').html(record.document_url
        ? `<a class="btn btn-outline-primary btn-sm" href="${escapeHtml(record.document_url)}" target="_blank" rel="noopener"><i class="fas fa-download mr-1"></i> Ver documento</a>`
        : '<span class="text-muted">Sin documento adjunto.</span>');
    setCattlePhoto(record.cattle_photo_url || null);
}

function resetVeterinaryRecordForm() {
    const form = document.getElementById('veterinaryRecordForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#veterinaryRecordForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#veterinaryRecordModalLabel').text('Nueva Revision');
    $('#saveVeterinaryRecordButton span').text('Guardar Revision');
    $('#currentDocumentLink').addClass('d-none').attr('href', '#');
    $('#documentFileName').text('Ningun archivo seleccionado');
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#veterinary-record-error-messages').addClass('d-none').empty();
    $('#veterinaryRecordForm .is-invalid').removeClass('is-invalid');
    $('#veterinaryRecordForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#veterinary-record-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function recordTypeBadge(type, label) {
    const classes = {
        checkup: 'badge-success',
        illness: 'badge-warning',
        control: 'badge-primary',
        certification: 'badge-info',
        emergency: 'badge-danger',
        other: 'badge-secondary'
    };

    return `<span class="badge ${classes[type] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailVeterinaryCattlePhoto');
    const $placeholder = $('#detailVeterinaryCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveVeterinaryRecordButton');

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
