var divLoading = document.getElementById('divLoading');
let tableGenealogy;
let genealogySubmitting = false;

const legacyRelationByPath = {
    F: 'father',
    M: 'mother',
    FF: 'paternal_grandfather',
    FM: 'paternal_grandmother',
    MF: 'maternal_grandfather',
    MM: 'maternal_grandmother'
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
            { data: 'lineage_path', name: 'lineage_path', orderable: false },
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

    $('#generation_level').on('change', function () {
        populateLineagePaths();
        filterRelativeCattle();
        updateLineageGuidance();
    });
    $('#lineage_path').on('change', function () {
        syncRelationTypeFromLineage();
        filterRelativeCattle();
        updateLineageGuidance();
    });
    $('#cattle_id').on('change', function () {
        filterRelativeCattle();
        updateLineageGuidance();
    });
    $('#relative_cattle_id').on('change', function () {
        fillRelativeData();
        updateLineageGuidance();
    });

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

                Swal.fire('Error', 'No se pudo guardar el registro genealogico. Intentelo nuevamente.', 'error');
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
                Swal.fire('Error', 'No se pudo cargar la informacion genealogica.', 'error');
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
                Swal.fire('Error', 'No se pudo cargar el detalle genealogico.', 'error');
            })
            .always(hideLoading);
    });

    $(document).on('click', '.deleteGenealogy', function () {
        const genealogyId = $(this).data('id');
        const relativeName = $(this).data('name') || 'este registro';

        Swal.fire({
            title: 'Eliminar registro genealogico?',
            text: `Se eliminara "${relativeName}".`,
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
                url: `${window.genealogyRoutes.index}/${genealogyId}`,
                type: 'DELETE',
                success: function (response) {
                    tableGenealogy.ajax.reload(null, false);
                    showToast(response.message);
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar el registro genealogico.', 'error');
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

    populateLineagePaths();
    updateLineageGuidance();
    openGenealogyFromQueryString();
});

function prepareEditForm(genealogy) {
    resetGenealogyForm();

    $('#genealogyForm').attr('data-id', genealogy.id);
    $('#genealogyModalLabel').text('Editar Registro Genealogico');
    $('#saveGenealogyButton span').text('Actualizar Registro');

    $('#cattle_id').val(genealogy.cattle_id ?? '');
    $('#generation_level').val(genealogy.generation_level ?? '1');
    populateLineagePaths(genealogy.lineage_path || lineagePathFromRelation(genealogy.relation_type));
    $('#relation_type').val(genealogy.relation_type ?? 'father');

    [
        'relative_cattle_id', 'relative_code', 'relative_name', 'breed_id', 'purity_percentage', 'notes'
    ].forEach(function (field) {
        $(`#${field}`).val(genealogy[field] ?? '');
    });

    filterRelativeCattle();
    updateLineageGuidance();
}

function generateLineagePaths(level) {
    const results = [];

    function build(path, depth) {
        if (depth === level) {
            results.push(path);
            return;
        }

        build(path + 'F', depth + 1);
        build(path + 'M', depth + 1);
    }

    build('', 0);

    return results;
}

function populateLineagePaths(selectedPath = null) {
    const level = Number($('#generation_level').val() || 1);
    const paths = generateLineagePaths(level);
    const $select = $('#lineage_path');
    const current = selectedPath || $select.val() || paths[0];

    $select.empty();
    paths.forEach(function (path) {
        $select.append(new Option(`${path} - ${lineageLabel(path)}`, path));
    });

    $select.val(paths.includes(current) ? current : paths[0]);
    syncRelationTypeFromLineage();
    updateLineageGuidance();
}

function syncRelationTypeFromLineage() {
    const path = $('#lineage_path').val();
    $('#relation_type').val(legacyRelationByPath[path] || 'lineage');
}

function lineagePathFromRelation(relation) {
    const entry = Object.entries(legacyRelationByPath).find(([, value]) => value === relation);

    return entry ? entry[0] : null;
}

function lineageLabel(path) {
    const map = {
        F: 'Padre directo',
        M: 'Madre directa',
        FF: 'Abuelo paterno (padre del padre)',
        FM: 'Abuela paterna (madre del padre)',
        MF: 'Abuelo materno (padre de la madre)',
        MM: 'Abuela materna (madre de la madre)'
    };

    if (map[path]) {
        return map[path];
    }

    const lastLetter = path.slice(-1);
    const prefix = lastLetter === 'F' ? 'Ancestro macho' : 'Ancestro hembra';
    return `${prefix} (${lineageDescription(path)})`;
}

function requiredSexFromLineage(path) {
    if (!path) {
        return null;
    }

    return path.endsWith('F') ? 'male' : 'female';
}

function filterRelativeCattle() {
    const path = $('#lineage_path').val();
    const requiredSex = requiredSexFromLineage(path);
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

    $('#relative_cattle_id-help').text(path
        ? `La ubicación ${path} requiere un familiar ${requiredSex === 'male' ? 'macho' : 'hembra'}.`
        : 'Seleccione primero la ubicación dentro del linaje.');
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

function updateLineageGuidance() {
    const mainAnimal = selectedCattle($('#cattle_id').val());
    const relative = selectedCattle($('#relative_cattle_id').val());
    const path = $('#lineage_path').val();
    const $help = $('#lineagePathHelp');
    const $preview = $('#lineageRelationPreview');
    const $previewText = $('#lineageRelationPreviewText');

    if (!path) {
        $help.empty();
        $preview.addClass('d-none');
        $previewText.empty();
        return;
    }

    const helpLines = [lineageHelpText(path, mainAnimal)];
    const intermediateWarning = lineageIntermediateWarning(path, mainAnimal);

    if (intermediateWarning) {
        helpLines.push(`<span class="text-danger">${escapeHtml(intermediateWarning)}</span>`);
    }

    $help.html(helpLines.join('<br>'));

    if (!mainAnimal) {
        $preview.addClass('d-none');
        $previewText.empty();
        return;
    }

    $preview.removeClass('d-none');
    $previewText.html(escapeHtml(lineagePreviewText(mainAnimal, path, relative)).replace(/ -> /g, ' <span class="text-muted">→</span> '));
}

function lineageHelpText(path, mainAnimal) {
    const mainName = mainAnimal ? cattleShortLabel(mainAnimal) : 'el animal principal';

    if (path === 'F') {
        return `Este familiar será registrado como padre directo de ${escapeHtml(mainName)}.`;
    }

    if (path === 'M') {
        return `Este familiar será registrado como madre directa de ${escapeHtml(mainName)}.`;
    }

    const parentPath = path.slice(0, -1);
    const lastStep = path.slice(-1);
    const relationship = lastStep === 'F' ? 'padre' : 'madre';
    const chain = lineageDescription(parentPath);
    const intermediate = mainAnimal ? resolveCattleByLineagePath(mainAnimal, parentPath) : null;
    const intermediateLabel = intermediate ? cattleShortLabel(intermediate) : 'no registrado';
    const intermediateName = parentPath.startsWith('F') ? 'Padre actual' : 'Madre actual';

    return `Este familiar será registrado como ${relationship} ${lineageTargetPhrase(chain)} de ${escapeHtml(mainName)}.<br>${intermediateName} de ${escapeHtml(mainName)}: ${escapeHtml(intermediateLabel)}.`;
}

function lineageIntermediateWarning(path, mainAnimal) {
    if (!mainAnimal || path.length < 2) {
        return null;
    }

    const parentPath = path.slice(0, -1);

    if (resolveCattleByLineagePath(mainAnimal, parentPath)) {
        return null;
    }

    if (path === 'FF' || path === 'FM') {
        return 'Primero debe registrar el padre del animal principal para poder asignar abuelos paternos.';
    }

    if (path === 'MF' || path === 'MM') {
        return 'Primero debe registrar la madre del animal principal para poder asignar abuelos maternos.';
    }

    return 'Faltan familiares intermedios registrados; se guardará el vínculo principal, pero no se podrá sincronizar toda la cadena.';
}

function lineagePreviewText(mainAnimal, path, relative) {
    const parts = [cattleShortLabel(mainAnimal)];
    let current = mainAnimal;

    path.split('').forEach(function (step, index) {
        const isLast = index === path.length - 1;
        const role = step === 'F' ? 'Padre' : 'Madre';
        const next = isLast ? relative : resolveNextCattle(current, step);
        parts.push(`${role}: ${next ? cattleShortLabel(next) : '[familiar seleccionado]'}`);

        if (!isLast) {
            current = next;
        }
    });

    return parts.join(' -> ');
}

function lineageDescription(path) {
    return path
        .split('')
        .reverse()
        .map(function (letter, index) {
            const role = letter === 'F' ? 'padre' : 'madre';

            if (index === 0) {
                return role;
            }

            return `${role === 'padre' ? 'del' : 'de la'} ${role}`;
        })
        .join(' ');
}

function lineageTargetPhrase(description) {
    if (!description) {
        return '';
    }

    return description.startsWith('padre')
        ? `del ${description}`
        : `de la ${description}`;
}

function resolveCattleByLineagePath(mainAnimal, path) {
    let current = mainAnimal;

    path.split('').forEach(function (step) {
        if (!current) {
            return;
        }

        current = resolveNextCattle(current, step);
    });

    return current || null;
}

function resolveNextCattle(animal, step) {
    if (!animal) {
        return null;
    }

    return step === 'F'
        ? selectedCattle(animal.father_id)
        : selectedCattle(animal.mother_id);
}

function cattleShortLabel(animal) {
    if (!animal) {
        return 'No registrado';
    }

    return `${animal.code ? `${animal.code} - ` : ''}${animal.name || 'Sin nombre'}`;
}

function validateGenealogyFormBeforeSubmit() {
    const mainCattleId = $('#cattle_id').val();
    const relativeCattleId = $('#relative_cattle_id').val();
    const path = $('#lineage_path').val();
    const requiredSex = requiredSexFromLineage(path);
    const relative = selectedCattle(relativeCattleId);

    if (!path) {
        showValidationErrors({
            lineage_path: ['Seleccione la posicion del linaje.']
        });
        return false;
    }

    if (path.length !== Number($('#generation_level').val())) {
        showValidationErrors({
            lineage_path: ['La ruta de linaje no coincide con la generacion seleccionada.']
        });
        return false;
    }

    const intermediateWarning = lineageIntermediateWarning(path, selectedCattle(mainCattleId));

    if (intermediateWarning && path.length === 2) {
        showValidationErrors({
            lineage_path: [intermediateWarning]
        });
        return false;
    }

    if (mainCattleId && relativeCattleId && String(mainCattleId) === String(relativeCattleId)) {
        showValidationErrors({
            relative_cattle_id: ['El familiar no puede ser el mismo animal principal.']
        });
        return false;
    }

    if (relative && requiredSex && relative.sex !== requiredSex) {
        showValidationErrors({
            relative_cattle_id: [sexMismatchMessage(path)]
        });
        return false;
    }

    const directParentError = directParentAsGrandparentMessage(path, mainCattleId, relativeCattleId);

    if (directParentError) {
        showValidationErrors({
            relative_cattle_id: [directParentError]
        });
        return false;
    }

    return true;
}

function directParentAsGrandparentMessage(path, mainCattleId, relativeCattleId) {
    if (!mainCattleId || !relativeCattleId || !path || path.length < 2) {
        return null;
    }

    const mainAnimal = selectedCattle(mainCattleId);

    if (!mainAnimal) {
        return null;
    }

    if (path.startsWith('F') && Number(mainAnimal.father_id) === Number(relativeCattleId)) {
        return 'El padre del animal no puede registrarse tambien como familiar de generaciones mayores.';
    }

    if (path.startsWith('M') && Number(mainAnimal.mother_id) === Number(relativeCattleId)) {
        return 'La madre del animal no puede registrarse tambien como familiar de generaciones mayores.';
    }

    if ([Number(mainAnimal.father_id), Number(mainAnimal.mother_id)].includes(Number(relativeCattleId))) {
        return 'Un padre o madre directo no puede registrarse en generaciones mayores del mismo animal.';
    }

    return null;
}

function sexMismatchMessage(path) {
    return path.endsWith('F')
        ? 'La posición seleccionada corresponde a un macho. Seleccione un animal macho.'
        : 'La posición seleccionada corresponde a una hembra. Seleccione un animal hembra.';
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
    $('#detailLineagePath').text(valueOrDash(genealogy.lineage_path));
    $('#detailLineageDescription').text(valueOrDash(genealogy.lineage_path_label));
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

    return `<span class="badge badge-${badgeClass} px-3 py-2">${escapeHtml(label || 'Relacion')}</span>`;
}

function resetGenealogyForm() {
    const form = document.getElementById('genealogyForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#genealogyForm').removeAttr('data-id data-preset');
    $('#generation_level').val('1');
    populateLineagePaths('F');
    $('#genealogyModalLabel').text('Nuevo Registro Genealogico');
    $('#saveGenealogyButton span').text('Guardar Registro');
    clearValidation();
    filterRelativeCattle();
    updateLineageGuidance();
}

function openGenealogyFromQueryString() {
    const params = new URLSearchParams(window.location.search);
    const cattleId = params.get('cattle_id');
    const relationType = params.get('relation_type');
    const lineagePath = params.get('lineage_path') || lineagePathFromRelation(relationType);

    if (!cattleId) {
        return;
    }

    resetGenealogyForm();
    $('#genealogyForm').attr('data-preset', '1');
    $('#cattle_id').val(cattleId);

    if (lineagePath) {
        $('#generation_level').val(String(lineagePath.length));
        populateLineagePaths(lineagePath);
    }

    filterRelativeCattle();
    updateLineageGuidance();
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
