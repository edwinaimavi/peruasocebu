var divLoading = document.getElementById('divLoading');
let tablePost;
// ============================
// LOCK ANTI DOBLE CLICK
// ============================
const submitLocks = {
    postSave: false
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

    //GENERAR EL SLUG

    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .normalize('NFD')                 // elimina acentos
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')     // elimina caracteres raros
            .replace(/\s+/g, '-')             // espacios por guiones
            .replace(/-+/g, '-');             // evita guiones dobles
    }


    let slugTouched = false;

    // si el usuario edita el slug manualmente, no lo tocamos más
    slugInput.addEventListener('input', function () {
        slugTouched = true;
    });

    titleInput.addEventListener('input', function () {

        // solo autogenerar si el slug está vacío o nunca fue tocado
        if (!slugTouched || slugInput.value === '') {
            slugInput.value = generateSlug(this.value);
        }
    });


    // ============================
    // DATATABLE POSTS
    // ============================
    tablePost = $('#tablePost').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.routes.postList,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'user_id', name: 'user.name', orderable: false },
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

    const imageInput = document.getElementById('image');
    const sidePreview = document.getElementById('postPreviewSide');

    imageInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            // SOLO actualizamos el preview lateral
            sidePreview.src = e.target.result;
        };

        reader.readAsDataURL(file);
    });

    // ============================
    // EDITAR POST
    // ============================

    let editLock = false;
    $(document).on('click', '.editPost', function () {

        const id = $(this).data('id');

        $('#postForm').attr('data-id', id);

        const url = `/admin/posts/${id}/edit`;

        divLoading && (divLoading.style.display = 'flex');

        $.get(url, function (res) {

            $('#title').val(res.title);
            $('#slug').val(res.slug);

            // ✅ HTML REAL EN TINYMCE
            tinymce.get('content').setContent(res.content || '');

            $('#category_id').val(res.category_id);
            $('#status').prop('checked', res.status === 'published');

            if (res.image) {
                $('#postPreviewSide').attr('src', res.image);
            }

            $('#postModalLabel').text('Editar Post');
            $('#postModal').modal('show');
        })
            .always(() => {
                divLoading && (divLoading.style.display = 'none');
            });
    });


    // ============================
    // GUARDAR / ACTUALIZAR POST
    // ============================
    $('#postForm').on('submit', function (e) {
        e.preventDefault();

        if (!lock('postSave')) return;

        // ✅ SINCRONIZA TINYMCE CON EL TEXTAREA
        tinymce.triggerSave();

        const $form = $(this);
        const $btn = $('#btnSavePost');

        // 🔒 bloquear UI
        $btn.prop('disabled', true).text('Guardando...');
        divLoading && (divLoading.style.display = 'flex');

        const id = $form.attr('data-id');

        let url;
        let formData = new FormData(this);

        if (id) {
            url = `/admin/posts/${id}`;
            formData.append('_method', 'PUT');
        } else {
            url = window.routes.storePost;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
                divLoading && (divLoading.style.display = 'none');
                unlock('postSave');

                $('#postModal').modal('hide');
                $form.trigger('reset').removeAttr('data-id');

                tablePost && tablePost.ajax.reload(null, false);

                Swal.fire({
                    icon: 'success',
                    title: response.message || 'Post guardado correctamente',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            },

            error: function (xhr) {
                divLoading && (divLoading.style.display = 'none');
                unlock('postSave');

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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Ocurrió un error al guardar el post.'
                    });
                }
            }
        });
    });



    //LIMPIAR AL CERRAR EL MODAL 

    $('#postModal').on('hidden.bs.modal', function () {
        unlock('postSave'); // 🔓 seguridad extra

        $('#postForm')[0].reset();
        $('#category_id').val('');
        $('#postMetaInfo').hide();

        $('#btnSavePost').prop('disabled', false).text('Guardar');

        document.getElementById('postPreviewSide').src =
            'https://static.vecteezy.com/system/resources/previews/005/951/722/non_2x/preview-interface-icon-illustration-vector.jpg';

        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });



    // ============================
    // VER POST (MODAL)
    // ============================
    // ============================
    // VER POST (MODAL)
    // ============================
    $(document).on('click', '.viewPost', function () {

        const id = $(this).data('id');
        const url = window.routes.postView.replace(':id', id);

        divLoading && (divLoading.style.display = 'flex');

        $.get(url, function (res) {

            $('#viewTitle').text(res.title);
            $('#viewSlug').text(res.slug);
            $('#viewCategory').text(res.category);
            $('#viewAuthor').text(res.author);
            $('#viewCreatedAt').text(res.created_at);
            $('#viewContent').html(res.content);

            if (res.image) {
                $('#viewImage').attr('src', res.image).removeClass('d-none');
            } else {
                $('#viewImage').addClass('d-none');
            }

            const modal = new bootstrap.Modal(
                document.getElementById('postViewModal')
            );
            modal.show();
        })
            .fail(() => {
                Swal.fire('Error', 'No se pudo cargar el post', 'error');
            })
            .always(() => {
                divLoading && (divLoading.style.display = 'none');
            });
    });


    // ============================
    // ELIMINAR POST
    // ============================
    $(document).on('click', '.deletePost', function () {

        const id = $(this).data('id');

        Swal.fire({
            title: '¿Eliminar post?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: `/admin/posts/${id}`,
                type: 'DELETE',

                success: function (res) {
                    tablePost.ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Post eliminado correctamente',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },

                error: function (xhr) {
                    Swal.fire(
                        'Error',
                        xhr.responseJSON?.message || 'No se pudo eliminar el post',
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