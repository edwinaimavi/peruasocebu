var divLoading = document.getElementById('divLoading');
let tableOwner;
let ownerSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableOwner = $('#tableOwner').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.ownerRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'owner_type', name: 'owner_type' },
            { data: 'document_number', name: 'document_number', defaultContent: '—' },
            { data: 'display_name', name: 'full_name' },
            { data: 'phone', name: 'phone', defaultContent: '—' },
            { data: 'email', name: 'email', defaultContent: '—' },
            { data: 'address', name: 'address', defaultContent: '—' },
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

    $('#owner_type').on('change', updateOwnerTypeFields);

    $('#ownerForm').on('submit', function (event) {
        event.preventDefault();

        if (ownerSubmitting) {
            return;
        }

        ownerSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const ownerId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.ownerRoutes.index;

        if (ownerId) {
            url = `${window.ownerRoutes.index}/${ownerId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#ownerModal').modal('hide');
                tableOwner.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el propietario. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                ownerSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editOwner', function () {
        const ownerId = $(this).data('id');
        showLoading();

        $.get(`${window.ownerRoutes.index}/${ownerId}`)
            .done(function (response) {
                prepareEditForm(response.owner);
                $('#ownerModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del propietario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewOwner', function () {
        const ownerId = $(this).data('id');
        showLoading();

        $.get(`${window.ownerRoutes.index}/${ownerId}`)
            .done(function (response) {
                fillDetailModal(response.owner);
                $('#ownerDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del propietario.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteOwner', function () {
        const ownerId = $(this).data('id');
        const ownerName = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar propietario?',
            text: `Se eliminará "${ownerName}".`,
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
                url: `${window.ownerRoutes.index}/${ownerId}`,
                type: 'DELETE',
                success: function (response) {
                    tableOwner.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el propietario.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#ownerModal').on('show.bs.modal', function () {
        if (!$('#ownerForm').attr('data-id')) {
            resetOwnerForm();
        }
    });

    $('#ownerModal').on('shown.bs.modal', function () {
        $('#owner_type').trigger('focus');
    });

    $('#ownerModal').on('hidden.bs.modal', resetOwnerForm);
});

function prepareEditForm(owner) {
    resetOwnerForm();

    $('#ownerForm').attr('data-id', owner.id);
    $('#ownerModalLabel').text('Editar Propietario');
    $('#saveOwnerButton span').text('Actualizar Propietario');

    [
        'owner_type', 'document_type', 'document_number', 'full_name',
        'business_name', 'phone', 'email', 'address', 'notes', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(owner[field] ?? '');
    });

    updateOwnerTypeFields();
}

function fillDetailModal(owner) {
    const isCompany = owner.owner_type === 'company';
    const displayName = isCompany && owner.business_name ? owner.business_name : owner.full_name;

    $('#detailOwnerSubtitle').text(`Registro #${owner.id}`);
    $('#detailDisplayName').text(valueOrDash(displayName));
    $('#detailContactName').text(
        isCompany ? `Contacto: ${valueOrDash(owner.full_name)}` : valueOrDash(owner.business_name)
    );
    $('#detailDocumentType').text(valueOrDash(owner.document_type_label));
    $('#detailDocumentNumber').text(valueOrDash(owner.document_number));
    $('#detailPhone').text(valueOrDash(owner.phone));
    $('#detailEmail').text(valueOrDash(owner.email));
    $('#detailAddress').text(valueOrDash(owner.address));
    $('#detailNotes').text(valueOrDash(owner.notes));
    $('#detailCreatedAt').text(valueOrDash(owner.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(owner.updated_at_formatted));
    $('#detailOwnerType').html(
        isCompany
            ? '<span class="badge badge-info px-3 py-2">Empresa</span>'
            : '<span class="badge badge-primary px-3 py-2">Persona</span>'
    );
    $('#detailStatus').html(
        owner.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );
}

function resetOwnerForm() {
    const form = document.getElementById('ownerForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#ownerForm').removeAttr('data-id');
    $('#owner_type').val('person');
    $('#status').val('active');
    $('#ownerModalLabel').text('Nuevo Propietario');
    $('#saveOwnerButton span').text('Guardar Propietario');
    clearValidation();
    updateOwnerTypeFields();
}

function updateOwnerTypeFields() {
    const isCompany = $('#owner_type').val() === 'company';

    $('#businessNameGroup').toggleClass('d-none', !isCompany);
    $('#fullNameLabel').text(isCompany ? 'Representante o contacto' : 'Nombre completo');
    $('#fullNameHelp').toggleClass('d-none', !isCompany);
}

function clearValidation() {
    $('#owner-error-messages').addClass('d-none').empty();
    $('#ownerForm .is-invalid').removeClass('is-invalid');
    $('#ownerForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#owner-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveOwnerButton');

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
