var divLoading = document.getElementById('divLoading');
let tableTreatment;
let treatmentSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableTreatment = $('#tableTreatment').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.treatmentRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'treatment_name', name: 'treatment_name' },
            { data: 'medicine', name: 'medicine', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'dose', name: 'dose', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'duration', name: 'duration' },
            { data: 'treatment_date', name: 'treatment_date' },
            { data: 'veterinarian_name', name: 'veterinarian.full_name', orderable: false },
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

    $('#treatmentForm').on('submit', submitTreatmentForm);
    $(document).on('click', '.editTreatment', editTreatment);
    $(document).on('click', '.viewTreatment', viewTreatment);
    $(document).on('click', '.deleteTreatment', deleteTreatment);

    $('#treatmentModal').on('show.bs.modal', function () {
        if (!$('#treatmentForm').attr('data-id')) {
            resetTreatmentForm();
        }
    });

    $('#treatmentModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#treatmentModal').on('hidden.bs.modal', resetTreatmentForm);
});

function submitTreatmentForm(event) {
    event.preventDefault();

    if (treatmentSubmitting) {
        return;
    }

    treatmentSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const formData = new FormData(event.currentTarget);
    const treatmentId = $('#treatmentForm').attr('data-id');
    let url = window.treatmentRoutes.index;

    if (treatmentId) {
        url = `${window.treatmentRoutes.index}/${treatmentId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#treatmentModal').modal('hide');
            tableTreatment.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar el tratamiento. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            treatmentSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editTreatment() {
    const treatmentId = $(this).data('id');
    showLoading();

    $.get(`${window.treatmentRoutes.index}/${treatmentId}`)
        .done(function (response) {
            prepareEditForm(response.treatment);
            $('#treatmentModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el tratamiento.', 'error');
        })
        .always(hideLoading);
}

function viewTreatment() {
    const treatmentId = $(this).data('id');
    showLoading();

    $.get(`${window.treatmentRoutes.index}/${treatmentId}`)
        .done(function (response) {
            fillDetailModal(response.treatment);
            $('#treatmentDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle del tratamiento.', 'error');
        })
        .always(hideLoading);
}

function deleteTreatment() {
    const treatmentId = $(this).data('id');
    const treatmentName = $(this).data('name') || 'este tratamiento';

    Swal.fire({
        title: 'Eliminar tratamiento?',
        text: `Se eliminara "${treatmentName}".`,
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
            url: `${window.treatmentRoutes.index}/${treatmentId}`,
            type: 'DELETE',
            success: function (response) {
                tableTreatment.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar el tratamiento.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(treatment) {
    resetTreatmentForm();

    $('#treatmentForm').attr('data-id', treatment.id);
    $('#treatmentModalLabel').text('Editar Tratamiento');
    $('#saveTreatmentButton span').text('Actualizar Tratamiento');

    [
        'cattle_id', 'veterinarian_id', 'treatment_date', 'treatment_name',
        'medicine', 'dose', 'duration', 'reason', 'observations'
    ].forEach(function (field) {
        $(`#${field}`).val(treatment[field] ?? '');
    });
}

function fillDetailModal(treatment) {
    $('#detailTreatmentSubtitle').text(`Registro #${treatment.id}`);
    $('#detailTreatmentCattle').text(valueOrDash(treatment.cattle_label));
    $('#detailTreatmentBreed').text(treatment.cattle_breed_name ? `Raza: ${treatment.cattle_breed_name}` : '-');
    $('#detailTreatmentOwner').text(treatment.cattle_owner_name ? `Propietario: ${treatment.cattle_owner_name}` : 'Propietario no registrado');
    $('#detailTreatmentName').text(valueOrDash(treatment.treatment_name));
    $('#detailTreatmentDate').text(valueOrDash(treatment.treatment_date_formatted));
    $('#detailTreatmentMedicine').text(treatment.medicine || 'No registrado');
    $('#detailTreatmentDose').text(valueOrDash(treatment.dose));
    $('#detailTreatmentDuration').text(valueOrDash(treatment.duration));
    $('#detailTreatmentVeterinarian').text(treatment.veterinarian_name || 'Sin veterinario asignado');
    $('#detailTreatmentLicense').text(valueOrDash(treatment.veterinarian_license));
    $('#detailTreatmentSpecialty').text(valueOrDash(treatment.veterinarian_specialty));
    $('#detailTreatmentReason').text(valueOrDash(treatment.reason));
    $('#detailTreatmentObservations').text(valueOrDash(treatment.observations));
    $('#detailTreatmentCreatedAt').text(valueOrDash(treatment.created_at_formatted));
    $('#detailTreatmentUpdatedAt').text(valueOrDash(treatment.updated_at_formatted));
    $('#detailTreatmentVeterinarianBadge').html(veterinarianBadge(Boolean(treatment.veterinarian_name)));
    $('#detailTreatmentDurationBadge').html(durationBadge(treatment.duration));
    setCattlePhoto(treatment.cattle_photo_url || null);
}

function resetTreatmentForm() {
    const form = document.getElementById('treatmentForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#treatmentForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#treatmentModalLabel').text('Nuevo Tratamiento');
    $('#saveTreatmentButton span').text('Guardar Tratamiento');
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#treatment-error-messages').addClass('d-none').empty();
    $('#treatmentForm .is-invalid').removeClass('is-invalid');
    $('#treatmentForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#treatment-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function veterinarianBadge(hasVeterinarian) {
    if (hasVeterinarian) {
        return '<span class="badge badge-info px-3 py-2">Con veterinario</span>';
    }

    return '<span class="badge badge-secondary px-3 py-2">Sin veterinario</span>';
}

function durationBadge(duration) {
    if (!duration) {
        return '';
    }

    return `<span class="badge badge-warning px-3 py-2">Duracion: ${escapeHtml(duration)}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailTreatmentCattlePhoto');
    const $placeholder = $('#detailTreatmentCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveTreatmentButton');
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
