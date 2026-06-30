var divLoading = document.getElementById('divLoading');
let tableBreed;
let breedSubmitting = false;
let breedImageObjectUrl = null;

const breedEditorIds = ['breed_description', 'breed_characteristics'];
const breedFieldIds = {
    description: 'breed_description',
    characteristics: 'breed_characteristics',
    image: 'breed_image'
};

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    initBreedRichEditors();

    tableBreed = $('#tableBreed').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.breedRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name', render: $.fn.dataTable.render.text() },
            { data: 'origin_country', name: 'origin_country', defaultContent: '—', render: $.fn.dataTable.render.text() },
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

    $('#name').on('input', function () {
        updateBreedCodePreview();
    });

    $('#breed_image').on('change', function () {
        const file = this.files?.[0];

        if (!file) {
            setBreedImagePreview(null);
            return;
        }

        setBreedImagePreview(URL.createObjectURL(file));
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
        syncBreedRichEditors();

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

    $(document).on('focusin', function (event) {
        if ($(event.target).closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root').length) {
            event.stopImmediatePropagation();
        }
    });
});

function initBreedRichEditors() {
    if (!window.tinymce) {
        return;
    }

    initTinyMceIfNeeded('#breed_description', {
        height: 180,
        placeholder: 'Origen, proposito productivo o descripcion general...',
        toolbar: 'undo redo | styleselect fontsizeselect | bold italic underline removeformat | bullist numlist | alignleft aligncenter alignright alignjustify | link | fullscreen'
    });

    initTinyMceIfNeeded('#breed_characteristics', {
        height: 220,
        placeholder: 'Caracteristicas fisicas, productivas o reproductivas...',
        toolbar: 'undo redo | styleselect fontsizeselect | bold italic underline strikethrough removeformat | forecolor backcolor | bullist numlist outdent indent | alignleft aligncenter alignright alignjustify | link | code fullscreen'
    });
}

function initTinyMceIfNeeded(selector, options = {}) {
    const id = selector.replace('#', '');

    if (tinymce.get(id)) {
        return;
    }

    tinymce.init({
        selector,
        menubar: false,
        branding: false,
        plugins: 'advlist autolink lists link charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime table wordcount',
        font_size_formats: '12px 14px 16px 18px 20px 24px 28px 32px 36px',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.7; }',
        setup: function (editor) {
            editor.on('change keyup undo redo', function () {
                editor.save();
            });
        },
        ...options
    });
}

function syncBreedRichEditors() {
    breedEditorIds.forEach(function (id) {
        const editor = tinymce.get(id);

        if (editor) {
            $(`#${id}`).val(editor.getContent());
        }
    });
}

function setBreedEditorContent(id, content) {
    const editor = tinymce.get(id);

    if (editor) {
        editor.setContent(content || '');
    } else {
        $(`#${id}`).val(content || '');
    }
}

function setBreedImagePreview(imageUrl) {
    if (breedImageObjectUrl) {
        URL.revokeObjectURL(breedImageObjectUrl);
        breedImageObjectUrl = null;
    }

    if (imageUrl?.startsWith('blob:')) {
        breedImageObjectUrl = imageUrl;
    }

    const $preview = $('#breedImagePreview');

    if (!$preview.length) {
        return;
    }

    if (imageUrl) {
        $preview.html(`<img src="${escapeHtml(imageUrl)}" alt="Vista previa de la raza">`);
        return;
    }

    $preview.html('<i class="fas fa-cow"></i><span>Sin imagen</span>');
}

function prepareEditForm(breed) {
    resetBreedForm();

    $('#breedForm').attr('data-id', breed.id);
    $('#breedForm').attr('data-original-name', breed.name ?? '');
    $('#breedModalLabel').text('Editar Raza');
    $('#saveBreedButton span').text('Actualizar Raza');

    [
        'name', 'code', 'origin_country', 'status'
    ].forEach(function (field) {
        $(`#${field}`).val(breed[field] ?? '');
    });

    setBreedEditorContent('breed_description', breed.description || '');
    setBreedEditorContent('breed_characteristics', breed.characteristics || '');
    setBreedImagePreview(breed.image_url || null);
}

function fillDetailModal(breed) {
    $('#detailBreedSubtitle').text(`Registro #${breed.id}`);
    $('#detailName').text(valueOrDash(breed.name));
    $('#detailCode').text(valueOrDash(breed.code));
    $('#detailOriginCountry').text(valueOrDash(breed.origin_country));
    $('#detailStatusText').text(valueOrDash(breed.status_label));
    $('#detailDescription').html(htmlOrDash(breed.description));
    $('#detailCharacteristics').html(htmlOrDash(breed.characteristics));
    $('#detailCreatedAt').text(valueOrDash(breed.created_at_formatted));
    $('#detailUpdatedAt').text(valueOrDash(breed.updated_at_formatted));
    setDetailImage(breed.image_url || null);
    $('#detailStatus').html(
        breed.status === 'active'
            ? '<span class="badge badge-success px-3 py-2">Activo</span>'
            : '<span class="badge badge-danger px-3 py-2">Inactivo</span>'
    );
}

function setDetailImage(imageUrl) {
    const $image = $('#detailBreedImage');
    const $placeholder = $('#detailBreedImagePlaceholder');

    if (imageUrl) {
        $image.attr('src', imageUrl).removeClass('d-none');
        $placeholder.addClass('d-none');
        return;
    }

    $image.attr('src', '').addClass('d-none');
    $placeholder.removeClass('d-none');
}

function resetBreedForm() {
    const form = document.getElementById('breedForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#breedForm').removeAttr('data-id data-original-name');
    $('#status').val('active');
    $('#breedModalLabel').text('Nueva Raza');
    $('#saveBreedButton span').text('Guardar Raza');
    setBreedEditorContent('breed_description', '');
    setBreedEditorContent('breed_characteristics', '');
    $('#breed_image').val('');
    setBreedImagePreview(null);
    clearValidation();
}

function updateBreedCodePreview() {
    const $form = $('#breedForm');
    const currentName = $('#name').val() || '';
    const originalName = $form.attr('data-original-name') || '';
    const isEditing = Boolean($form.attr('data-id'));

    if (isEditing && normalizeBreedName(currentName) === normalizeBreedName(originalName)) {
        return;
    }

    const prefix = buildBreedCodePrefix(currentName);

    $('#code').val(prefix ? `${prefix}001` : '');
}

function buildBreedCodePrefix(name) {
    const letters = normalizeBreedName(name).slice(0, 2);

    if (!letters) {
        return '';
    }

    return letters.padEnd(2, 'X');
}

function normalizeBreedName(name) {
    return String(name)
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-zA-Z]/g, '')
        .toUpperCase();
}

function clearValidation() {
    $('#breed-error-messages').addClass('d-none').empty();
    $('#breedForm .is-invalid').removeClass('is-invalid');
    $('#breedForm .tox-tinymce.is-invalid').removeClass('is-invalid');
    $('#breedForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        const inputId = breedFieldIds[field] || field;

        messages.push(fieldMessages[0]);
        $(`#${inputId}`).addClass('is-invalid');
        $(`#${inputId}`).siblings('.tox-tinymce').addClass('is-invalid');
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

function htmlOrDash(value) {
    return value || '&mdash;';
}

function escapeHtml(value) {
    return $('<div>').text(value).html();
}
