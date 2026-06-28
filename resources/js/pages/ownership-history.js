var divLoading = document.getElementById('divLoading');
let tableOwnershipHistory;
let ownershipHistorySubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableOwnershipHistory = $('#tableOwnershipHistory').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.ownershipHistoryRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'owner_name', name: 'owner.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'acquisition_type', name: 'acquisition_type', orderable: false },
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'price', name: 'price', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'is_current', name: 'is_current', orderable: false, searchable: false },
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

    $('#ownershipHistoryForm').on('submit', submitOwnershipHistoryForm);

    $(document).on('click', '.editOwnershipHistory', function () {
        const historyId = $(this).data('id');
        showLoading();

        $.get(`${window.ownershipHistoryRoutes.index}/${historyId}`)
            .done(function (response) {
                prepareEditForm(response.history);
                $('#ownershipHistoryModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el historial.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewOwnershipHistory', function () {
        const historyId = $(this).data('id');
        showLoading();

        $.get(`${window.ownershipHistoryRoutes.index}/${historyId}`)
            .done(function (response) {
                fillDetailModal(response.history);
                $('#ownershipHistoryDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del historial.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteOwnershipHistory', function () {
        const historyId = $(this).data('id');
        const historyName = $(this).data('name') || 'este historial';

        Swal.fire({
            title: 'Eliminar historial?',
            text: `Se eliminara "${historyName}".`,
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
                url: `${window.ownershipHistoryRoutes.index}/${historyId}`,
                type: 'DELETE',
                success: function (response) {
                    tableOwnershipHistory.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el historial.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#ownershipHistoryModal').on('show.bs.modal', function () {
        if (!$('#ownershipHistoryForm').attr('data-id')) {
            resetOwnershipHistoryForm();
        }
    });

    $('#ownershipHistoryModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#ownershipHistoryModal').on('hidden.bs.modal', resetOwnershipHistoryForm);

    $('#is_current').on('change', function () {
        if ($(this).is(':checked')) {
            $('#end_date').val('');
        }
    });
});

function submitOwnershipHistoryForm(event) {
    event.preventDefault();

    if (ownershipHistorySubmitting) {
        return;
    }

    ownershipHistorySubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const $form = $(event.currentTarget);
    const historyId = $form.attr('data-id');
    const formData = new FormData(event.currentTarget);
    let url = window.ownershipHistoryRoutes.index;

    if (!$('#is_current').is(':checked')) {
        formData.set('is_current', '0');
    }

    if (historyId) {
        url = `${window.ownershipHistoryRoutes.index}/${historyId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#ownershipHistoryModal').modal('hide');
            tableOwnershipHistory.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar el historial. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            ownershipHistorySubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function prepareEditForm(history) {
    resetOwnershipHistoryForm();

    $('#ownershipHistoryForm').attr('data-id', history.id);
    $('#ownershipHistoryModalLabel').text('Editar Historial');
    $('#saveOwnershipHistoryButton span').text('Actualizar Historial');

    [
        'cattle_id', 'owner_id', 'start_date', 'end_date', 'acquisition_type',
        'document_reference', 'price', 'currency', 'notes'
    ].forEach(function (field) {
        $(`#${field}`).val(history[field] ?? '');
    });

    $('#is_current').prop('checked', Boolean(history.is_current));
}

function fillDetailModal(history) {
    $('#detailOwnershipSubtitle').text(`Registro #${history.id}`);
    $('#detailOwnershipCattle').text(valueOrDash(history.cattle_label));
    $('#detailOwnershipBreed').text(history.cattle_breed_name ? `Raza: ${history.cattle_breed_name}` : '-');
    $('#detailOwnershipOwner').text(valueOrDash(history.owner_name));
    $('#detailOwnershipOwnerDocument').text(valueOrDash(history.owner_document));
    $('#detailOwnershipOwnerPhone').text(valueOrDash(history.owner_phone));
    $('#detailOwnershipOwnerEmail').text(valueOrDash(history.owner_email));
    $('#detailOwnershipStartDate').text(valueOrDash(history.start_date_formatted));
    $('#detailOwnershipEndDate').text(valueOrDash(history.end_date_formatted));
    $('#detailOwnershipDocumentReference').text(valueOrDash(history.document_reference));
    $('#detailOwnershipPrice').text(valueOrDash(history.price_formatted));
    $('#detailOwnershipCreatedAt').text(valueOrDash(history.created_at_formatted));
    $('#detailOwnershipUpdatedAt').text(valueOrDash(history.updated_at_formatted));
    $('#detailOwnershipNotes').text(valueOrDash(history.notes));
    $('#detailOwnershipCurrentBadge').html(history.is_current
        ? '<span class="badge badge-success px-3 py-2">Actual</span>'
        : '<span class="badge badge-secondary px-3 py-2">Historico</span>');
    $('#detailOwnershipTypeBadge').html(acquisitionBadge(history.acquisition_type, history.acquisition_type_label));
    setCattlePhoto(history.cattle_photo_url || null);
}

function acquisitionBadge(type, label) {
    const classes = {
        birth: 'badge-success',
        purchase: 'badge-primary',
        sale: 'badge-info',
        transfer: 'badge-warning',
        donation: 'badge-light border',
        other: 'badge-secondary'
    };

    return `<span class="badge ${classes[type] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailOwnershipCattlePhoto');
    const $placeholder = $('#detailOwnershipCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function resetOwnershipHistoryForm() {
    const form = document.getElementById('ownershipHistoryForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#ownershipHistoryForm').removeAttr('data-id');
    $('#currency').val('PEN');
    $('#cattle_id').val(defaultCattleIdFromUrl());
    $('#ownershipHistoryModalLabel').text('Nuevo Historial');
    $('#saveOwnershipHistoryButton span').text('Guardar Historial');
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#ownership-history-error-messages').addClass('d-none').empty();
    $('#ownershipHistoryForm .is-invalid').removeClass('is-invalid');
    $('#ownershipHistoryForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#ownership-history-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveOwnershipHistoryButton');

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
