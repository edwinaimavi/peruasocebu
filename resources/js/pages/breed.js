var divLoading = document.getElementById('divLoading');
let tableBreed;
let breedSubmitting = false;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableBreed = $('#tableBreed').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.breedRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'origin_country', name: 'origin_country', defaultContent: '—' },
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

    $('#code').on('input', function () {
        this.value = this.value.toUpperCase().replace(/\s+/g, '');
    });

    $('#breedForm').on('submit', function (event) {
        event.preventDefault();

        if (breedSubmitting) {
            return;
        }

        breedSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const breedId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.breedRoutes.index;

        if (breedId) {
            url = `${window.breedRoutes.index}/${breedId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#breedModal').modal('hide');
                tableBreed.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar la raza. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                breedSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editBreed', function () {
        const breedId = $(this).data('id');
        showLoading();

        $.get(`${window.breedRoutes.index}/${breedId}`)
            .done(function (response) {
                prepareEditForm(response.breed);
                $('#breedModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información de la raza.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewBreed', function () {
        const breedId = $(this).data('id');
        showLoading();

        $.get(`${window.breedRoutes.index}/${breedId}`)
            .done(function (response) {
                fillDetailModal(response.breed);
                $('#breedDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle de la raza.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteBreed', function () {
        const breedId = $(this).data('id');
        const breedName = $(this).data('name');

        Swal.fire({
            title: '¿Eliminar raza?',
            text: `Se eliminará "${breedName}".`,
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
                url: `${window.breedRoutes.index}/${breedId}`,
                type: 'DELETE',
                success: function (response) {
                    tableBreed.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar la raza.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#breedModal').on('show.bs.modal', function () {
        if (!$('#breedForm').attr('data-id')) {
            resetBreedForm();
        }
    });

    $('#breedModal').on('shown.bs.modal', function () {
        $('#name').trigger('focus');
    });

    $('#breedModal').on('hidden.bs.modal', resetBreedForm);
});

function prepareEditForm(breed) {
    resetBreedForm();

    $('#breedForm').attr('data-id', breed.id);
    $('#breedModalLabel').text('Editar Raza');
    $('#saveBreedButton span').text('Actualizar Raza');

    [
        'name', 'code', 'origin_country', 'description', 'characteristics', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(breed[field] ?? '');
    });
}

function fillDetailModal(breed) {
    $('#detailBreedSubtitle').text(`Registro #${breed.id}`);
    $('#detailName').text(valueOrDash(breed.name));
    $('#detailCode').text(valueOrDash(breed.code));
    $('#detailOriginCountry').text(valueOrDash(breed.origin_country));
    $('#detailStatusText').text(valueOrDash(breed.status_label));
    $('#detailDescription').text(valueOrDash(breed.description));
    $('#detailCharacteristics').text(valueOrDash(breed.characteristics));
    $('#detailCreatedAt').text(valueOrDash(breed.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(breed.updated_at_formatted));
    $('#detailStatus').html(
        breed.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );
}

function resetBreedForm() {
    const form = document.getElementById('breedForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#breedForm').removeAttr('data-id');
    $('#status').val('active');
    $('#breedModalLabel').text('Nueva Raza');
    $('#saveBreedButton span').text('Guardar Raza');
    clearValidation();
}

function clearValidation() {
    $('#breed-error-messages').addClass('d-none').empty();
    $('#breedForm .is-invalid').removeClass('is-invalid');
    $('#breedForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#breed-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveBreedButton');

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
