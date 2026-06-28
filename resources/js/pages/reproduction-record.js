var divLoading = document.getElementById('divLoading');
let tableReproductionRecord;
let reproductionRecordSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableReproductionRecord = $('#tableReproductionRecord').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.reproductionRecordRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'partner_name', name: 'partner.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'method', name: 'method', orderable: false },
            { data: 'reproduction_date', name: 'reproduction_date' },
            { data: 'pregnancy_result', name: 'pregnancy_result', orderable: false },
            { data: 'birth_date', name: 'birth_date', orderable: false },
            { data: 'offspring_name', name: 'offspring.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
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

    $('#reproductionRecordForm').on('submit', submitReproductionRecordForm);
    $('#pregnancy_result').on('change', toggleBirthFields);
    $(document).on('click', '.editReproductionRecord', editReproductionRecord);
    $(document).on('click', '.viewReproductionRecord', viewReproductionRecord);
    $(document).on('click', '.deleteReproductionRecord', deleteReproductionRecord);

    $('#reproductionRecordModal').on('show.bs.modal', function () {
        if (!$('#reproductionRecordForm').attr('data-id')) {
            resetReproductionRecordForm();
        }
    });

    $('#reproductionRecordModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#reproductionRecordModal').on('hidden.bs.modal', resetReproductionRecordForm);
});

function submitReproductionRecordForm(event) {
    event.preventDefault();

    if (reproductionRecordSubmitting) {
        return;
    }

    reproductionRecordSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const form = event.currentTarget;
    const recordId = $('#reproductionRecordForm').attr('data-id');
    const formData = new FormData(form);
    let url = window.reproductionRecordRoutes.index;

    if (recordId) {
        url = `${window.reproductionRecordRoutes.index}/${recordId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#reproductionRecordModal').modal('hide');
            tableReproductionRecord.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar el registro reproductivo. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            reproductionRecordSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editReproductionRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.reproductionRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            prepareEditForm(response.record);
            $('#reproductionRecordModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el registro reproductivo.', 'error');
        })
        .always(hideLoading);
}

function viewReproductionRecord() {
    const recordId = $(this).data('id');
    showLoading();

    $.get(`${window.reproductionRecordRoutes.index}/${recordId}`)
        .done(function (response) {
            fillDetailModal(response.record);
            $('#reproductionRecordDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle reproductivo.', 'error');
        })
        .always(hideLoading);
}

function deleteReproductionRecord() {
    const recordId = $(this).data('id');
    const recordName = $(this).data('name') || 'este registro';

    Swal.fire({
        title: 'Eliminar registro?',
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
            url: `${window.reproductionRecordRoutes.index}/${recordId}`,
            type: 'DELETE',
            success: function (response) {
                tableReproductionRecord.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar el registro reproductivo.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(record) {
    resetReproductionRecordForm();

    $('#reproductionRecordForm').attr('data-id', record.id);
    $('#reproductionRecordModalLabel').text('Editar Registro');
    $('#saveReproductionRecordButton span').text('Actualizar Registro');

    [
        'cattle_id', 'partner_cattle_id', 'method', 'reproduction_date',
        'pregnancy_check_date', 'pregnancy_result', 'birth_date',
        'offspring_cattle_id', 'observations'
    ].forEach(function (field) {
        $(`#${field}`).val(record[field] ?? '');
    });

    toggleBirthFields();
}

function fillDetailModal(record) {
    $('#detailReproductionSubtitle').text(`Registro #${record.id}`);
    $('#detailReproductionCattle').text(valueOrDash(record.cattle_label));
    $('#detailReproductionCattleMeta').text(`${valueOrDash(record.cattle_sex_label)} | Raza: ${valueOrDash(record.cattle_breed_name)}`);
    $('#detailReproductionOwner').text(record.cattle_owner_name ? `Propietario: ${record.cattle_owner_name}` : 'Propietario no registrado');
    $('#detailReproductionPartner').text(valueOrDash(record.partner_label));
    $('#detailReproductionPartnerMeta').text(record.partner_code
        ? `${valueOrDash(record.partner_sex_label)} | Raza: ${valueOrDash(record.partner_breed_name)}`
        : 'Sin pareja registrada');
    $('#detailReproductionOffspring').text(valueOrDash(record.offspring_label));
    $('#detailReproductionMethod').text(valueOrDash(record.method_label));
    $('#detailReproductionDate').text(valueOrDash(record.reproduction_date_formatted));
    $('#detailPregnancyCheckDate').text(valueOrDash(record.pregnancy_check_date_formatted));
    $('#detailPregnancyResult').text(valueOrDash(record.pregnancy_result_label));
    $('#detailBirthDate').text(valueOrDash(record.birth_date_formatted));
    $('#detailReproductionObservations').text(valueOrDash(record.observations));
    $('#detailReproductionCreatedAt').text(valueOrDash(record.created_at_formatted));
    $('#detailReproductionUpdatedAt').text(valueOrDash(record.updated_at_formatted));
    $('#detailReproductionMethodBadge').html(methodBadge(record.method, record.method_label));
    $('#detailReproductionResultBadge').html(pregnancyResultBadge(record.pregnancy_result, record.pregnancy_result_label));
    $('#detailBirthBadge').html(record.birth_date
        ? '<span class="badge badge-success px-3 py-2">Parto registrado</span>'
        : '<span class="badge badge-secondary px-3 py-2">Sin parto</span>');
    setCattlePhoto(record.cattle_photo_url || null);
}

function resetReproductionRecordForm() {
    const form = document.getElementById('reproductionRecordForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#reproductionRecordForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#pregnancy_result').val('pending');
    $('#reproductionRecordModalLabel').text('Nuevo Registro');
    $('#saveReproductionRecordButton span').text('Guardar Registro');
    toggleBirthFields();
    clearValidation();
}

function toggleBirthFields() {
    const isNegative = $('#pregnancy_result').val() === 'negative';

    $('#birth_date, #offspring_cattle_id')
        .prop('disabled', isNegative)
        .toggleClass('bg-light', isNegative);

    if (isNegative) {
        $('#birth_date, #offspring_cattle_id').val('');
    }
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#reproduction-record-error-messages').addClass('d-none').empty();
    $('#reproductionRecordForm .is-invalid').removeClass('is-invalid');
    $('#reproductionRecordForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#reproduction-record-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function methodBadge(method, label) {
    const classes = {
        natural_mating: 'badge-success',
        artificial_insemination: 'badge-info',
        embryo_transfer: 'badge-warning'
    };

    return `<span class="badge ${classes[method] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function pregnancyResultBadge(result, label) {
    const classes = {
        positive: 'badge-success',
        negative: 'badge-danger',
        pending: 'badge-warning'
    };

    return `<span class="badge ${classes[result] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailReproductionCattlePhoto');
    const $placeholder = $('#detailReproductionCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveReproductionRecordButton');

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
