var divLoading = document.getElementById('divLoading');
let tableCattle;
let cattleSubmitting = false;
let cattlePhotoObjectUrl = null;
let currentCattlePhotoUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableCattle = $('#tableCattle').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.cattleRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name', render: $.fn.dataTable.render.text() },
            { data: 'breed_name', name: 'breed.name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'sex', name: 'sex', orderable: false },
            { data: 'ranch_name', name: 'ranch.name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'owner_name', name: 'currentOwner.full_name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'purity_percentage', name: 'purity_percentage', defaultContent: '—' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'sale_status', name: 'sale_status', orderable: false },
            { data: 'is_public', name: 'is_public', orderable: false },
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

    $('#breed_id').on('change', updateCattleCodePreview);
    $('#main_photo').on('change', handlePhotoChange);
    $('#btnRemoveCattlePhotoPreview').on('click', clearSelectedCattlePhoto);

    $('#cattleForm').on('submit', function (event) {
        event.preventDefault();

        if (cattleSubmitting) {
            return;
        }

        cattleSubmitting = true;
        clearValidation();
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const cattleId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.cattleRoutes.index;

        if (!$('#is_public').is(':checked')) {
            formData.set('is_public', '0');
        }

        if (cattleId) {
            url = `${window.cattleRoutes.index}/${cattleId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#cattleModal').modal('hide');
                tableCattle.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el ganado. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                cattleSubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editCattle', function () {
        const cattleId = $(this).data('id');
        showLoading();

        $.get(`${window.cattleRoutes.index}/${cattleId}`)
            .done(function (response) {
                prepareEditForm(response.cattle);
                $('#cattleModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información del ganado.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewCattle', function () {
        const cattleId = $(this).data('id');
        showLoading();

        $.get(`${window.cattleRoutes.index}/${cattleId}`)
            .done(function (response) {
                fillDetailModal(response.cattle);
                $('#cattleDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle del ganado.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteCattle', function () {
        const cattleId = $(this).data('id');
        const cattleName = $(this).data('name') || 'este registro';

        Swal.fire({
            title: '¿Eliminar ganado?',
            text: `Se eliminará "${cattleName}".`,
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
                url: `${window.cattleRoutes.index}/${cattleId}`,
                type: 'DELETE',
                success: function (response) {
                    tableCattle.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el ganado.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#cattleModal').on('show.bs.modal', function () {
        if (!$('#cattleForm').attr('data-id')) {
            resetCattleForm();
        }
    });

    $('#cattleModal').on('shown.bs.modal', function () {
        $('#name').trigger('focus');
    });

    $('#cattleModal').on('hidden.bs.modal', resetCattleForm);
});

function prepareEditForm(cattle) {
    resetCattleForm();

    $('#cattleForm').attr('data-id', cattle.id);
    $('#cattleModalLabel').text('Editar Ganado');
    $('#saveCattleButton span').text('Actualizar Ganado');

    [
        'code', 'name', 'breed_id', 'ranch_id', 'current_owner_id', 'father_id',
        'mother_id', 'sex', 'birth_date', 'color', 'weight_kg', 'height_cm',
        'ear_tag', 'chip_number', 'purity_percentage', 'status', 'sale_status',
        'observations'
    ].forEach(function (field) {
        $(`#${field}`).val(cattle[field] ?? '');
    });

    $('#is_public').prop('checked', Boolean(cattle.is_public));
    currentCattlePhotoUrl = cattle.photo_url || null;
    setCattlePhotoPreview(currentCattlePhotoUrl);
}

function handlePhotoChange() {
    const file = this.files && this.files[0];

    if (!file) {
        setCattlePhotoPreview(null);
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    setCattlePhotoPreview(objectUrl, {
        fileName: file.name,
        isObjectUrl: true,
        removable: true
    });
}

function setCattlePhotoPreview(url, options = {}) {
    if (cattlePhotoObjectUrl) {
        URL.revokeObjectURL(cattlePhotoObjectUrl);
        cattlePhotoObjectUrl = null;
    }

    if (options.isObjectUrl) {
        cattlePhotoObjectUrl = url;
    }

    const $preview = $('#mainPhotoPreview');
    const $placeholder = $('#mainPhotoPlaceholder');
    const $fileName = $('#mainPhotoFileName');
    const $removeButton = $('#btnRemoveCattlePhotoPreview');

    if (!url) {
        $preview.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        $fileName.text('Ningún archivo seleccionado');
        $removeButton.addClass('d-none');
        return;
    }

    $preview.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
    $fileName.text(options.fileName || 'Foto actual');
    $removeButton.toggleClass('d-none', !options.removable);
}

function clearSelectedCattlePhoto() {
    $('#main_photo').val('');
    setCattlePhotoPreview(currentCattlePhotoUrl);
}

function updateCattleCodePreview() {
    const cattleId = $('#cattleForm').attr('data-id');

    if (cattleId && $('#code').val()) {
        return;
    }

    const breed = selectedBreed();

    if (!breed) {
        $('#code').val('');
        return;
    }

    $('#code').val(`${breedCodePrefix(breed)}-000001`);
}

function selectedBreed() {
    const breedId = Number($('#breed_id').val());

    return (window.cattleBreeds || []).find(function (breed) {
        return Number(breed.id) === breedId;
    });
}

function breedCodePrefix(breed) {
    const base = breed.code || breed.name || 'GAN';
    const prefix = String(base)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-zA-Z0-9]/g, '')
        .toUpperCase();

    return prefix || 'GAN';
}

function fillDetailModal(cattle) {
    $('#detailCattleSubtitle').text(`Registro #${cattle.id}`);
    $('#detailName').text(valueOrDash(cattle.name));
    $('#detailCode').text(valueOrDash(cattle.code));
    $('#detailBreed').text(valueOrDash(cattle.breed_name));
    $('#detailBirthDate').text(valueOrDash(cattle.birth_date_formatted));
    $('#detailAge').text(valueOrDash(cattle.age_text || cattle.age_label));
    $('#detailColor').text(valueOrDash(cattle.color));
    $('#detailRanch').text(valueOrDash(cattle.ranch_name));
    $('#detailOwner').text(valueOrDash(cattle.owner_name));
    $('#detailPurity').text(cattle.purity_percentage !== null && cattle.purity_percentage !== undefined ? `${cattle.purity_percentage}%` : '—');
    $('#detailWeight').text(cattle.weight_kg ? `${cattle.weight_kg} kg` : '—');
    $('#detailHeight').text(cattle.height_cm ? `${cattle.height_cm} cm` : '—');
    $('#detailEarTag').text(valueOrDash(cattle.ear_tag));
    $('#detailChip').text(valueOrDash(cattle.chip_number));
    $('#detailObservations').text(valueOrDash(cattle.observations));
    $('#detailCreatedAt').text(valueOrDash(cattle.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(cattle.updated_at_formatted));
    $('#detailFather').text(valueOrDash(cattle.father_label || 'No registrado'));
    $('#detailFatherBreed').text(cattle.father_breed_name ? `Raza: ${cattle.father_breed_name}` : 'No registrado');
    $('#detailMother').text(valueOrDash(cattle.mother_label || 'No registrado'));
    $('#detailMotherBreed').text(cattle.mother_breed_name ? `Raza: ${cattle.mother_breed_name}` : 'No registrado');
    setCattleDetailPhoto(cattle.photo_url || null);

    $('#detailSexBadge').html(cattle.sex === 'male'
        ? '<span class="badge badge-primary px-3 py-2">Macho</span>'
        : '<span class="badge badge-info px-3 py-2">Hembra</span>');
    $('#detailStatusBadge').html(statusBadge(cattle.status));
    $('#detailSaleStatusBadge').html(saleStatusBadge(cattle.sale_status));
    $('#detailPublicBadge').html(cattle.is_public
        ? '<span class="badge badge-success px-3 py-2">Público</span>'
        : '<span class="badge badge-secondary px-3 py-2">Privado</span>');
}

function setCattleDetailPhoto(url) {
    const $photo = $('#detailMainPhoto');
    const $placeholder = $('#detailMainPhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function statusBadge(status) {
    if (status === 'active') {
        return '<span class="badge badge-success px-3 py-2">Activo</span>';
    }

    if (status === 'dead') {
        return '<span class="badge badge-danger px-3 py-2">Fallecido</span>';
    }

    return '<span class="badge badge-warning px-3 py-2">Descartado</span>';
}

function saleStatusBadge(status) {
    if (status === 'available') {
        return '<span class="badge badge-success px-3 py-2">Disponible</span>';
    }

    if (status === 'reserved') {
        return '<span class="badge badge-warning px-3 py-2">Reservado</span>';
    }

    if (status === 'sold') {
        return '<span class="badge badge-info px-3 py-2">Vendido</span>';
    }

    return '<span class="badge badge-secondary px-3 py-2">No disponible</span>';
}

function resetCattleForm() {
    const form = document.getElementById('cattleForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#cattleForm').removeAttr('data-id');
    $('#code').val('');
    $('#status').val('active');
    $('#sale_status').val('not_available');
    $('#is_public').prop('checked', true);
    $('#cattleModalLabel').text('Nuevo Ganado');
    $('#saveCattleButton span').text('Guardar Ganado');
    currentCattlePhotoUrl = null;
    clearValidation();
    setCattlePhotoPreview(null);
}

function clearValidation() {
    $('#cattle-error-messages').addClass('d-none').empty();
    $('#cattleForm .is-invalid').removeClass('is-invalid');
    $('#cattleForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#cattle-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveCattleButton');

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
