var divLoading = document.getElementById('divLoading');
let tableGenealogy;
let genealogySubmitting = false;

const generationByRelation = {
    father: '1',
    mother: '1',
    paternal_grandfather: '2',
    paternal_grandmother: '2',
    maternal_grandfather: '2',
    maternal_grandmother: '2'
};

const sexByRelation = {
    father: 'male',
    mother: 'female',
    paternal_grandfather: 'male',
    paternal_grandmother: 'female',
    maternal_grandfather: 'male',
    maternal_grandmother: 'female'
};

const relationSexHelp = {
    father: 'Para padre solo se permiten animales machos.',
    mother: 'Para madre solo se permiten animales hembras.',
    paternal_grandfather: 'Debe seleccionar al padre del padre del animal principal. No seleccione al padre directo.',
    paternal_grandmother: 'Debe seleccionar a la madre del padre del animal principal.',
    maternal_grandfather: 'Debe seleccionar al padre de la madre del animal principal.',
    maternal_grandmother: 'Debe seleccionar a la madre de la madre del animal principal.'
};

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableGenealogy = $('#tableGenealogy').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.genealogyRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', render: $.fn.dataTable.render.text() },
            { data: 'relation_type', name: 'relation_type', orderable: false },
            { data: 'generation_level', name: 'generation_level', orderable: false },
            { data: 'relative_name', name: 'relative_name', render: $.fn.dataTable.render.text() },
            { data: 'breed_name', name: 'breed.name', defaultContent: '—', render: $.fn.dataTable.render.text() },
            { data: 'purity_percentage', name: 'purity_percentage', defaultContent: '—' },
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

    $('#relation_type').on('change', function () {
        syncGenerationLevel();
        filterRelativeCattle();
    });
    $('#cattle_id').on('change', filterRelativeCattle);
    $('#relative_cattle_id').on('change', fillRelativeData);

    $('#genealogyForm').on('submit', function (event) {
        event.preventDefault();

        if (genealogySubmitting) {
            return;
        }

        clearValidation();

        if (!validateGenealogyFormBeforeSubmit()) {
            return;
        }

        genealogySubmitting = true;
        setSaveButtonLoading(true);
        showLoading();

        const $form = $(this);
        const genealogyId = $form.attr('data-id');
        const formData = new FormData(this);
        let url = window.genealogyRoutes.index;

        if (genealogyId) {
            url = `${window.genealogyRoutes.index}/${genealogyId}`;
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#genealogyModal').modal('hide');
                tableGenealogy.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors(xhr.responseJSON.errors || {});
                    return;
                }

                Swal.fire('Error', 'No se pudo guardar el registro genealógico. Inténtelo nuevamente.', 'error');
            },
            complete: function () {
                genealogySubmitting = false;
                setSaveButtonLoading(false);
                hideLoading();
            }
        });
    });

    $(document).on('click', '.editGenealogy', function () {
        const genealogyId = $(this).data('id');
        showLoading();

        $.get(`${window.genealogyRoutes.index}/${genealogyId}`)
            .done(function (response) {
                prepareEditForm(response.genealogy);
                $('#genealogyModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar la información genealógica.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.viewGenealogy', function () {
        const genealogyId = $(this).data('id');
        showLoading();

        $.get(`${window.genealogyRoutes.index}/${genealogyId}`)
            .done(function (response) {
                fillDetailModal(response.genealogy);
                $('#genealogyDetailModal').modal('show');
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cargar el detalle genealógico.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteGenealogy', function () {
        const genealogyId = $(this).data('id');
        const relativeName = $(this).data('name') || 'este registro';

        Swal.fire({
            title: '¿Eliminar registro genealógico?',
            text: `Se eliminará "${relativeName}".`,
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
                url: `${window.genealogyRoutes.index}/${genealogyId}`,
                type: 'DELETE',
                success: function (response) {
                    tableGenealogy.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el registro genealógico.', 'error');
                },
                complete: hideLoading
            });
        });
    });

    $('#genealogyModal').on('show.bs.modal', function () {
        if (!$('#genealogyForm').attr('data-id') && !$('#genealogyForm').attr('data-preset')) {
            resetGenealogyForm();
        }
    });

    $('#genealogyModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#genealogyModal').on('hidden.bs.modal', resetGenealogyForm);

    openGenealogyFromQueryString();
});

function prepareEditForm(genealogy) {
    resetGenealogyForm();

    $('#genealogyForm').attr('data-id', genealogy.id);
    $('#genealogyModalLabel').text('Editar Registro Genealógico');
    $('#saveGenealogyButton span').text('Actualizar Registro');

    [
        'cattle_id', 'relative_cattle_id', 'relation_type', 'generation_level',
        'relative_code', 'relative_name', 'breed_id', 'purity_percentage', 'notes'
    ].forEach(function (field) {
        $(`#${field}`).val(genealogy[field] ?? '');
    });

    filterRelativeCattle();
}

function syncGenerationLevel() {
    const relation = $('#relation_type').val();

    if (generationByRelation[relation]) {
        $('#generation_level').val(generationByRelation[relation]);
    }
}

function filterRelativeCattle() {
    const relation = $('#relation_type').val();
    const requiredSex = sexByRelation[relation] || null;
    const mainCattleId = $('#cattle_id').val();
    const $relativeSelect = $('#relative_cattle_id');
    const selectedOption = $relativeSelect.find('option:selected');
    const selectedSex = selectedOption.data('sex');
    const selectedRelativeId = $relativeSelect.val();

    $relativeSelect.find('option').each(function () {
        const $option = $(this);
        const optionValue = String($option.val() || '');
        const optionSex = $option.data('sex');
        const isManualOption = !optionValue;
        const matchesSex = !requiredSex || optionSex === requiredSex;
        const isDifferentAnimal = !mainCattleId || optionValue !== String(mainCattleId);
        const canShow = isManualOption || (matchesSex && isDifferentAnimal);

        $option.prop('disabled', !canShow).toggleClass('d-none', !canShow);
    });

    if (
        selectedRelativeId
        && ((requiredSex && selectedSex !== requiredSex) || String(selectedRelativeId) === String(mainCattleId))
    ) {
        $relativeSelect.val('');
        clearRelativeData();
    }

    $('#relative_cattle_id-help').text(relationSexHelp[relation]
        || 'Si la relación es Madre, aquí debes seleccionar la hembra que será madre del animal principal. Si la relación es Padre, selecciona el macho que será padre.');
}

function fillRelativeData() {
    const relative = selectedCattle($('#relative_cattle_id').val());

    if (!relative) {
        clearRelativeData();
        return;
    }

    $('#relative_code').val(relative.code || '');
    $('#relative_name').val(relative.name || '');
    $('#breed_id').val(relative.breed_id || '');
    $('#purity_percentage').val(relative.purity_percentage || '');
}

function clearRelativeData() {
    $('#relative_code').val('');
    $('#relative_name').val('');
    $('#breed_id').val('');
    $('#purity_percentage').val('');
}

function validateGenealogyFormBeforeSubmit() {
    const mainCattleId = $('#cattle_id').val();
    const relativeCattleId = $('#relative_cattle_id').val();
    const relation = $('#relation_type').val();
    const requiredSex = sexByRelation[relation] || null;
    const relative = selectedCattle(relativeCattleId);

    if (mainCattleId && relativeCattleId && String(mainCattleId) === String(relativeCattleId)) {
        showValidationErrors({
            relative_cattle_id: ['El familiar no puede ser el mismo animal principal.']
        });
        return false;
    }

    if (relative && requiredSex && relative.sex !== requiredSex) {
        showValidationErrors({
            relative_cattle_id: [sexMismatchMessage(relation, relative.sex)]
        });
        return false;
    }

    const directParentError = directParentAsGrandparentMessage(relation, mainCattleId, relativeCattleId);

    if (directParentError) {
        showValidationErrors({
            relative_cattle_id: [directParentError]
        });
        return false;
    }

    return true;
}

function directParentAsGrandparentMessage(relation, mainCattleId, relativeCattleId) {
    if (!mainCattleId || !relativeCattleId) {
        return null;
    }

    const mainAnimal = selectedCattle(mainCattleId);

    if (!mainAnimal) {
        return null;
    }

    if (
        ['paternal_grandfather', 'paternal_grandmother'].includes(relation)
        && Number(mainAnimal.father_id) === Number(relativeCattleId)
    ) {
        return 'El padre del animal no puede registrarse también como abuelo paterno.';
    }

    if (
        ['maternal_grandfather', 'maternal_grandmother'].includes(relation)
        && Number(mainAnimal.mother_id) === Number(relativeCattleId)
    ) {
        return 'La madre del animal no puede registrarse también como abuela materna.';
    }

    if (
        ['paternal_grandfather', 'paternal_grandmother', 'maternal_grandfather', 'maternal_grandmother'].includes(relation)
        && [Number(mainAnimal.father_id), Number(mainAnimal.mother_id)].includes(Number(relativeCattleId))
    ) {
        return 'Un padre o madre directo no puede registrarse como abuelo o abuela del mismo animal.';
    }

    return null;
}

function sexMismatchMessage(relation, selectedSex) {
    if (relation === 'mother') {
        return 'La madre debe ser un animal hembra.';
    }

    if (relation === 'father') {
        return 'El padre debe ser un animal macho.';
    }

    if (sexByRelation[relation] === 'male') {
        return 'El abuelo debe ser un animal macho.';
    }

    return 'La abuela debe ser un animal hembra.';
}

function selectedCattle(id) {
    const cattleId = Number(id);

    return (window.genealogyCattle || []).find(function (animal) {
        return Number(animal.id) === cattleId;
    });
}

function fillDetailModal(genealogy) {
    $('#detailGenealogySubtitle').text(`Registro #${genealogy.id}`);
    $('#detailMainAnimal').text(valueOrDash(genealogy.cattle_label));
    $('#detailMainBreed').text(genealogy.cattle_breed_name ? `Raza: ${genealogy.cattle_breed_name}` : 'Raza no registrada');
    $('#detailMainRanch').text(valueOrDash(genealogy.cattle_ranch_name));
    $('#detailMainOwner').text(valueOrDash(genealogy.cattle_owner_name));
    $('#detailRegisteredRelative').text(valueOrDash(genealogy.relative_registered_label));
    $('#detailRelativeCode').text(valueOrDash(genealogy.relative_display_code));
    $('#detailRelativeName').text(valueOrDash(genealogy.relative_name));
    $('#detailRelativeBreed').text(valueOrDash(genealogy.relative_breed_name));
    $('#detailRelativePurity').text(genealogy.purity_percentage !== null && genealogy.purity_percentage !== undefined ? `${genealogy.purity_percentage}%` : '—');
    $('#detailNotes').text(valueOrDash(genealogy.notes));
    $('#detailGenealogyCreatedAt').text(valueOrDash(genealogy.created_at_formatted));
    $('#detailGenealogyUpdatedAt').text(valueOrDash(genealogy.updated_at_formatted));
    $('#detailFlowMain').text(valueOrDash(genealogy.cattle_label));
    $('#detailFlowRelation').text(valueOrDash(genealogy.relation_label));
    $('#detailFlowRelative').text(valueOrDash(genealogy.relative_display_name));
    $('#detailRelationBadge').html(relationBadge(genealogy.relation_type, genealogy.relation_label));
    $('#detailGenerationBadge').html(`<span class="badge badge-light border px-3 py-2">${escapeHtml(genealogy.generation_label || '—')}</span>`);
    setDetailPhoto(genealogy.cattle_photo_url || null);
}

function setDetailPhoto(url) {
    const $photo = $('#detailGenealogyPhoto');
    const $placeholder = $('#detailGenealogyPhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function relationBadge(type, label) {
    const badgeClass = ['father', 'mother'].includes(type) ? 'success' : 'info';

    return `<span class="badge badge-${badgeClass} px-3 py-2">${escapeHtml(label || 'Relación')}</span>`;
}

function resetGenealogyForm() {
    const form = document.getElementById('genealogyForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#genealogyForm').removeAttr('data-id data-preset');
    $('#generation_level').val('1');
    $('#genealogyModalLabel').text('Nuevo Registro Genealógico');
    $('#saveGenealogyButton span').text('Guardar Registro');
    clearValidation();
    filterRelativeCattle();
}

function openGenealogyFromQueryString() {
    const params = new URLSearchParams(window.location.search);
    const cattleId = params.get('cattle_id');
    const relationType = params.get('relation_type');

    if (!cattleId) {
        return;
    }

    resetGenealogyForm();
    $('#genealogyForm').attr('data-preset', '1');
    $('#cattle_id').val(cattleId);

    if (relationType) {
        $('#relation_type').val(relationType);
        syncGenerationLevel();
    }

    filterRelativeCattle();
    $('#genealogyModal').modal('show');
}

function clearValidation() {
    $('#genealogy-error-messages').addClass('d-none').empty();
    $('#genealogyForm .is-invalid').removeClass('is-invalid');
    $('#genealogyForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#genealogy-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveGenealogyButton');

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
