var divLoading = document.getElementById('divLoading');
let tableCategory;

// ============================
// LOCK ANTI DOBLE CLICK
// ============================
const submitLocks = {
    categorySave: false
};

function lock(action) {
    if (submitLocks[action]) return false;
    submitLocks[action] = true;
    return true;
}

function unlock(action) {
    submitLocks[action] = false;
}


document.addEventListener("DOMContentLoaded", function () {



    // CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //VARIABLES  PARA EL LLENADO AUTOMATICO DEL CAMPO SLUG
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');


    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .normalize('NFD')                 // elimina acentos
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')     // elimina caracteres raros
            .replace(/\s+/g, '-')              // espacios por guiones
            .replace(/-+/g, '-');              // evita guiones dobles
    }

    nameInput.addEventListener('input', function () {
        slugInput.value = generateSlug(this.value);
    });



    // ============================
    // DATATABLE CATEGORIES
    // ============================
    tableCategory = $('#tableCategory').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.categoryList,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'description', name: 'description' },
            { data: 'status', name: 'status', orderable: false },
            { data: 'acciones', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        language: {
            url: "/vendor/datatables/js/i18n/es-ES.json"
        },
        preDrawCallback: function () {
            divLoading && divLoading.classList.remove('d-none');
        },
        drawCallback: function () {
            divLoading && divLoading.classList.add('d-none');
        }
    });

    // ============================
    // EDITAR CATEGORY
    // ============================
    $(document).on('click', '.editCategory', function () {

        const $btn = $(this);

        $('#categoryForm').attr('data-id', $btn.data('id'));

        $('#name').val($btn.data('name'));
        $('#slug').val($btn.data('slug'));
        $('#description').val($btn.data('description'));
        $('#status').prop('checked', $btn.data('status') == 1);

        // limpiar errores
        $('#categoryForm .is-invalid').removeClass('is-invalid');
        $('#categoryForm .invalid-feedback').text('');

        $('#categoryModalLabel').text('Editar Categoría');
        $('#categoryModal').modal('show');
    });

    $('#categoryModal').on('show.bs.modal', function () {
        const $form = $('#categoryForm');

        if (!$form.attr('data-id')) {
            $form[0].reset();
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').text('');

            $('#status').prop('checked', true);
            $('#categoryModalLabel').text('Nueva Categoría');
        }
    });

    // ============================
    //   GUARDAR / ACTUALIZAR CATEGORÍA
    // ============================
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();

        // ⛔ evitar doble envío
        if (!lock('categorySave')) return;

        divLoading && (divLoading.style.display = 'flex');

        const $form = $(this);
        const id = $form.attr('data-id');

        let url, method;
        const formData = $form.serialize();

        if (id) {
            url = `/admin/categories/${id}`;
            method = 'POST';
        } else {
            url = window.routes.storeCategory; // define esto en blade
            method = 'POST';
        }

        let dataToSend = formData;
        if (id) dataToSend = formData + '&_method=PUT';

        $.ajax({
            url: url,
            type: method,
            data: dataToSend,
            success: function (response) {
                divLoading && (divLoading.style.display = 'none');
                unlock('categorySave');

                $('#categoryModal').modal('hide');
                $form.trigger('reset').removeAttr('data-id');

                tableCategory && tableCategory.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: response.message || 'Categoría guardada correctamente',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            },
            error: function (xhr) {
                divLoading && (divLoading.style.display = 'none');
                unlock('categorySave');

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};

                    $form.find('.is-invalid').removeClass('is-invalid');
                    $form.find('.invalid-feedback').text('');

                    $.each(errors, function (field, messages) {
                        const input = $('#' + field);
                        if (input.length) {
                            input.addClass('is-invalid');
                            $('#' + field + '-error').text(messages[0]);
                        }
                    });
                } else {
                    console.error('Error al guardar categoría', xhr);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Ocurrió un error al guardar la categoría.'
                    });
                }
            }
        });
    });


    // ============================
    //   LIMPIAR MODAL CATEGORÍA
    // ============================
    $('#categoryModal').on('hidden.bs.modal', function () {
        const $form = $('#categoryForm');

        // Reset campos
        $form.trigger('reset');

        // Quitar modo edición
        $form.removeAttr('data-id');

        // Limpiar errores de validación
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        // Estado por defecto
        $('#status').prop('checked', true);

        // Slug editable nuevamente
        $('#slug').prop('readonly', false);
    });


    // ============================
    // ELIMINAR CATEGORY
    // ============================
    $(document).on('click', '.deleteCategory', function () {

        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar categoría?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/admin/categories/${id}`,
                type: 'DELETE',
                success: function (res) {
                    tableCategory.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Categoría eliminada',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                },
                error: function () {
                    Swal.fire('Error', 'No se pudo eliminar la categoría', 'error');
                }
            });
        });
    });

});
