var divLoading = document.getElementById('divLoading');
let tableWeightRecord;
let weightRecordSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableWeightRecord = $('#tableWeightRecord').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.weightRecordRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'weight_kg', name: 'weight_kg', orderable: false },
            { data: 'record_date', name: 'record_date' },
            { data: 'body_condition', name: 'body_condition', orderable: false },
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

    $('#weightRecordForm').on('submit', submitWeightRecordForm);
    $(document).on('click', '.editWeightRecord', editWeightRecord);
    $(document).on('click', '.viewWeightRecord', viewWeightRecord);
    $(document).on('click', '.deleteWeightRecord', deleteWeightRecord);

    $('#weightRecordModal').on('show.bs.modal', function () {
        if (!$('#weightRecordForm').attr('data-id')) {
            resetWeightRecordForm();
        }
    });

    $('#weightRecordModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#weightRecordModal').on('hidden.bs.modal', resetWeightRecordForm);
});

function submitWeightRecordForm(event) {
    event.preventDefault();

    if (weightRecordSubmitting) {
        return;
    }

    weightRecordSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const form = event.currentTarget;
    const recordId = $('#weightRecordForm').attr('data-id');
    const formData = new FormData(form);
    let url = window.weightRecordRoutes.index;

    if (recordId) {
        url = `${window.weightRecordRoutes.index}/${recordId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#weightRecordModal').modal('hide');
            tableWeightRecord.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar el pesaje. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            weightRecordSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editWeightRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.weightRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            prepareEditForm(response.record);
            $('#weightRecordModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el pesaje.', 'error');
        })
        .always(hideLoading);
}

function viewWeightRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.weightRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            fillDetailModal(response.record);
            $('#weightRecordDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle del pesaje.', 'error');
        })
        .always(hideLoading);
}

function deleteWeightRecord() {
    const recordId = $(this).data('id');
    const recordName = $(this).data('name') || 'este pesaje';

    Swal.fire({
        title: 'Eliminar pesaje?',
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
            url: `${window.weightRecordRoutes.index}/${recordId}`,
            type: 'DELETE',
            success: function (response) {
                tableWeightRecord.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar el pesaje.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(record) {
    resetWeightRecordForm();

    $('#weightRecordForm').attr('data-id', record.id);
    $('#weightRecordModalLabel').text('Editar Pesaje');
    $('#saveWeightRecordButton span').text('Actualizar Pesaje');

    ['cattle_id', 'weight_kg', 'record_date', 'body_condition', 'observations'].forEach(function (field) {
        $(`#${field}`).val(record[field] ?? '');
    });
}

function fillDetailModal(record) {
    $('#detailWeightRecordSubtitle').text(`Registro #${record.id}`);
    $('#detailWeightCattle').text(valueOrDash(record.cattle_label));
    $('#detailWeightBreed').text(record.cattle_breed_name ? `Raza: ${record.cattle_breed_name}` : '-');
    $('#detailWeightOwner').text(record.cattle_owner_name ? `Propietario: ${record.cattle_owner_name}` : 'Propietario no registrado');
    $('#detailWeightKg').text(valueOrDash(record.weight_kg_formatted));
    $('#detailWeightRecordDate').text(valueOrDash(record.record_date_formatted));
    $('#detailWeightBodyCondition').text(valueOrDash(record.body_condition_label));
    $('#detailPreviousWeight').text(valueOrDash(record.previous_weight_kg_formatted));
    $('#detailWeightEvolution').text(weightEvolutionText(record));
    $('#detailWeightObservations').text(valueOrDash(record.observations));
    $('#detailWeightCreatedAt').text(valueOrDash(record.created_at_formatted));
    $('#detailWeightUpdatedAt').text(valueOrDash(record.updated_at_formatted));
    $('#detailWeightBadge').html(weightBadge(record.weight_kg_formatted));
    $('#detailBodyConditionBadge').html(bodyConditionBadge(record.body_condition));
    setCattlePhoto(record.cattle_photo_url || null);
}

function resetWeightRecordForm() {
    const form = document.getElementById('weightRecordForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#weightRecordForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#weightRecordModalLabel').text('Nuevo Pesaje');
    $('#saveWeightRecordButton span').text('Guardar Pesaje');
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#weight-record-error-messages').addClass('d-none').empty();
    $('#weightRecordForm .is-invalid').removeClass('is-invalid');
    $('#weightRecordForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#weight-record-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function bodyConditionBadge(condition) {
    const key = String(condition || '').toLowerCase();
    const classes = {
        excelente: 'badge-success',
        buena: 'badge-info',
        regular: 'badge-warning',
        baja: 'badge-orange',
        critica: 'badge-danger'
    };

    return `<span class="badge ${classes[key] || 'badge-secondary'} px-3 py-2">${escapeHtml(condition || 'Sin dato')}</span>`;
}

function weightBadge(weight) {
    return `<span class="badge badge-light border px-3 py-2">${escapeHtml(weight || '-')}</span>`;
}

function weightEvolutionText(record) {
    if (record.weight_difference === null || record.weight_difference === undefined) {
        return 'Sin pesaje anterior para comparar.';
    }

    const difference = Number(record.weight_difference);
    const abs = Math.abs(difference).toFixed(2);

    if (difference > 0) {
        return `Subio ${abs} kg desde el ultimo pesaje.`;
    }

    if (difference < 0) {
        return `Bajo ${abs} kg desde el ultimo pesaje.`;
    }

    return 'No tuvo variacion frente al ultimo pesaje.';
}

function setCattlePhoto(url) {
    const $photo = $('#detailWeightCattlePhoto');
    const $placeholder = $('#detailWeightCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveWeightRecordButton');

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
