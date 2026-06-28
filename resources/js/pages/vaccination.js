var divLoading = document.getElementById('divLoading');
let tableVaccination;
let vaccinationSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableVaccination = $('#tableVaccination').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.vaccinationRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'vaccine_name', name: 'vaccine_name' },
            { data: 'dose', name: 'dose', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'batch_number', name: 'batch_number', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'application_date', name: 'application_date' },
            { data: 'next_due_date', name: 'next_due_date' },
            { data: 'veterinarian_name', name: 'veterinarian.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
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

    $('#vaccinationForm').on('submit', submitVaccinationForm);
    $(document).on('click', '.editVaccination', editVaccination);
    $(document).on('click', '.viewVaccination', viewVaccination);
    $(document).on('click', '.deleteVaccination', deleteVaccination);

    $('#vaccinationModal').on('show.bs.modal', function () {
        if (!$('#vaccinationForm').attr('data-id')) {
            resetVaccinationForm();
        }
    });

    $('#vaccinationModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#vaccinationModal').on('hidden.bs.modal', resetVaccinationForm);
});

function submitVaccinationForm(event) {
    event.preventDefault();

    if (vaccinationSubmitting) {
        return;
    }

    vaccinationSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const formData = new FormData(event.currentTarget);
    const vaccinationId = $('#vaccinationForm').attr('data-id');
    let url = window.vaccinationRoutes.index;

    if (vaccinationId) {
        url = `${window.vaccinationRoutes.index}/${vaccinationId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#vaccinationModal').modal('hide');
            tableVaccination.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la vacuna. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            vaccinationSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editVaccination() {
    const vaccinationId = $(this).data('id');
    showLoading();

    $.get(`${window.vaccinationRoutes.index}/${vaccinationId}`)
        .done(function (response) {
            prepareEditForm(response.vaccination);
            $('#vaccinationModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la vacuna.', 'error');
        })
        .always(hideLoading);
}

function viewVaccination() {
    const vaccinationId = $(this).data('id');
    showLoading();

    $.get(`${window.vaccinationRoutes.index}/${vaccinationId}`)
        .done(function (response) {
            fillDetailModal(response.vaccination);
            $('#vaccinationDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle de la vacuna.', 'error');
        })
        .always(hideLoading);
}

function deleteVaccination() {
    const vaccinationId = $(this).data('id');
    const vaccinationName = $(this).data('name') || 'esta vacuna';

    Swal.fire({
        title: 'Eliminar vacuna?',
        text: `Se eliminara "${vaccinationName}".`,
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
            url: `${window.vaccinationRoutes.index}/${vaccinationId}`,
            type: 'DELETE',
            success: function (response) {
                tableVaccination.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la vacuna.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(vaccination) {
    resetVaccinationForm();

    $('#vaccinationForm').attr('data-id', vaccination.id);
    $('#vaccinationModalLabel').text('Editar Vacuna');
    $('#saveVaccinationButton span').text('Actualizar Vacuna');

    [
        'cattle_id', 'veterinarian_id', 'vaccine_name', 'dose', 'batch_number',
        'application_date', 'next_due_date', 'observations'
    ].forEach(function (field) {
        $(`#${field}`).val(vaccination[field] ?? '');
    });
}

function fillDetailModal(vaccination) {
    $('#detailVaccinationSubtitle').text(`Registro #${vaccination.id}`);
    $('#detailVaccinationCattle').text(valueOrDash(vaccination.cattle_label));
    $('#detailVaccinationBreed').text(vaccination.cattle_breed_name ? `Raza: ${vaccination.cattle_breed_name}` : '-');
    $('#detailVaccinationOwner').text(vaccination.cattle_owner_name ? `Propietario: ${vaccination.cattle_owner_name}` : 'Propietario no registrado');
    $('#detailVaccineName').text(valueOrDash(vaccination.vaccine_name));
    $('#detailDose').text(valueOrDash(vaccination.dose));
    $('#detailBatchNumber').text(valueOrDash(vaccination.batch_number));
    $('#detailApplicationDate').text(valueOrDash(vaccination.application_date_formatted));
    $('#detailNextDueDate').text(valueOrDash(vaccination.next_due_date_formatted));
    $('#detailVaccinationVeterinarian').text(valueOrDash(vaccination.veterinarian_name));
    $('#detailVaccinationLicense').text(valueOrDash(vaccination.veterinarian_license));
    $('#detailVaccinationObservations').text(valueOrDash(vaccination.observations));
    $('#detailVaccinationCreatedAt').text(valueOrDash(vaccination.created_at_formatted));
    $('#detailVaccinationUpdatedAt').text(valueOrDash(vaccination.updated_at_formatted));
    $('#detailNextDueBadge').html(nextDueBadge(vaccination.next_due_status, vaccination.next_due_status_label));
    setCattlePhoto(vaccination.cattle_photo_url || null);
}

function resetVaccinationForm() {
    const form = document.getElementById('vaccinationForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#vaccinationForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#vaccinationModalLabel').text('Nueva Vacuna');
    $('#saveVaccinationButton span').text('Guardar Vacuna');
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#vaccination-error-messages').addClass('d-none').empty();
    $('#vaccinationForm .is-invalid').removeClass('is-invalid');
    $('#vaccinationForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#vaccination-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function nextDueBadge(status, label) {
    const classes = {
        none: 'badge-secondary',
        scheduled: 'badge-info',
        today: 'badge-warning',
        overdue: 'badge-danger'
    };

    return `<span class="badge ${classes[status] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailVaccinationCattlePhoto');
    const $placeholder = $('#detailVaccinationCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveVaccinationButton');
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
