var divLoading = document.getElementById('divLoading');
let tablePage;

// ============================
// LOCK ANTI DOBLE CLICK
// ============================
const submitLocks = {
    pageSave: false
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

    // ============================
    // CSRF
    // ============================
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ============================
    // SLUG
    // ============================
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    let slugTouched = false;

    slugInput.addEventListener('input', () => slugTouched = true);

    titleInput.addEventListener('input', function () {
        if (!slugTouched || slugInput.value === '') {
            slugInput.value = generateSlug(this.value);
        }
    });

    // ============================
    // DATATABLE PAGES
    // ============================
    tablePage = $('#tablePage').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.pageList,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'slug', name: 'slug', orderable: false },
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
    // EDITAR PAGE
    // ============================
    $(document).on('click', '.editPage', function () {

        const id = $(this).data('id');
        $('#pageForm').attr('data-id', id);

        divLoading && (divLoading.style.display = 'flex');

        $.get(`/admin/pages/${id}/edit`, function (res) {

            $('#title').val(res.title);
            $('#slug').val(res.slug);

            // ✅ HTML REAL
            tinymce.get('content').setContent(res.content || '');

            $('#status').prop('checked', res.status === 'published');

            $('#pageModalLabel').text('Editar Página');
            $('#pageModal').modal('show');
        })
            .always(() => {
                divLoading && (divLoading.style.display = 'none');
            });
    });



    // ============================
    // GUARDAR / ACTUALIZAR PAGE
    // ============================
    $('#pageForm').on('submit', function (e) {
        e.preventDefault();
        if (!lock('pageSave')) return;

        tinymce.triggerSave();

        const form = this;
        const id = $(this).attr('data-id');
        let formData = new FormData(form);
        let url = window.routes.pageStore;

        if (id) {
            url = `/admin/pages/${id}`;
            formData.append('_method', 'PUT');
        }

        divLoading && (divLoading.style.display = 'flex');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (res) {
                unlock('pageSave');
                divLoading && (divLoading.style.display = 'none');

                $('#pageModal').modal('hide');
                form.reset();
                $(form).removeAttr('data-id');

                tablePage.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: res.message || 'Página guardada',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            },

            error: function (xhr) {
                unlock('pageSave');
                divLoading && (divLoading.style.display = 'none');

                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Error al guardar la página',
                    'error'
                );
            }
        });
    });


    // ============================
    // LIMPIAR AL CERRAR MODAL PAGE
    // ============================
    $('#pageModal').on('hidden.bs.modal', function () {

        unlock('pageSave'); // 🔓 seguridad extra

        // Reset formulario
        $('#pageForm')[0].reset();
        $('#pageForm').removeAttr('data-id');

        // Reset título del modal
        $('#pageModalLabel').text('Nueva Página');

        // Reset switch estado
        $('#status').prop('checked', false);

        // Limpiar TinyMCE
        if (tinymce.get('content')) {
            tinymce.get('content').setContent('');
        }

        // Habilitar botón guardar
        $('#btnSavePage')
            .prop('disabled', false)
            .html('<i class="fas fa-save mr-1"></i> Guardar Página');

        // Limpiar validaciones
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });

    $(document).on('click', '.viewPage', function () {

        const id = $(this).data('id');
        const url = window.routes.pageView.replace(':id', id);

        divLoading && (divLoading.style.display = 'flex');

        $.get(url, function (res) {

            $('#viewPageTitle').text(res.title);
            $('#viewPageSlug').text(res.slug);

            $('#viewPageStatus').html(
                res.status === 'published'
                    ? '<span class="badge badge-success">Publicado</span>'
                    : '<span class="badge badge-secondary">Borrador</span>'
            );

            $('#viewPageCreatedAt').text(res.created_at);

            // HTML REAL
            $('#viewPageContent').html(res.content);

            // ✅ BOOTSTRAP 4
            $('#pageViewModal').modal('show');
        })
            .fail(() => {
                Swal.fire('Error', 'No se pudo cargar la página', 'error');
            })
            .always(() => {
                divLoading && (divLoading.style.display = 'none');
            });
    });






    // ============================
    // ELIMINAR PAGE
    // ============================
    $(document).on('click', '.deletePage', function () {

        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar página?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: `/admin/pages/${id}`,
                type: 'DELETE',

                success: function (res) {
                    tablePage.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Página eliminada',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                },

                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'No se pudo eliminar la página',
                        'error'
                    );
                }
            });
        });
    });
    $(document).on('focusin', function (e) {
        if ($(e.target).closest(".tox-dialog").length) {
            e.stopImmediatePropagation();
        }
    });

    tinymce.init({
        selector: '#content',
        width: "100%",
        height: 400,
        statubar: true,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "save table contextmenu directionality emoticons template paste textcolor"
        ],
        toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
    });
});
