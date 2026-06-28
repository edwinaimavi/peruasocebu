var divLoading = document.getElementById('divLoading');
let tableBlogPost;
let blogPostSubmitting = false;
let blogImageObjectUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    initRichEditor();

    tableBlogPost = $('#tableBlogPost').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.blogPostRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'title', name: 'title', render: $.fn.dataTable.render.text() },
            { data: 'slug', name: 'slug', render: $.fn.dataTable.render.text() },
            { data: 'author_name', name: 'author.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'status', name: 'status', orderable: false },
            { data: 'published_at', name: 'published_at' },
            { data: 'created_at', name: 'created_at' },
            { data: 'acciones', name: 'acciones', orderable: false, searchable: false }
        ],
        responsive: true,
        autoWidth: false,
        order: [[1, 'desc']],
        language: { url: '/vendor/datatables/js/i18n/es-ES.json' },
        dom: `
            <'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>
            <'row'<'col-sm-12'tr>>
            <'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-center justify-content-md-end'p>>
            <'row mt-3'<'col-sm-12 text-center'B>>
        `,
        buttons: [
            { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fas fa-file-excel"></i> Excel', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'pdf', className: 'btn btn-danger btn-sm', text: '<i class="fas fa-file-pdf"></i> PDF', exportOptions: { columns: ':not(:last-child)' } },
            { extend: 'print', className: 'btn btn-secondary btn-sm', text: '<i class="fas fa-print"></i> Imprimir', exportOptions: { columns: ':not(:last-child)' } }
        ],
        preDrawCallback: showLoading,
        drawCallback: hideLoading
    });

    $('#blogPostForm').on('submit', submitBlogPostForm);
    $('#title').on('input', updateSlugPreview);
    $('#image_file').on('change', previewSelectedImage);

    $(document).on('click', '.editBlogPost', editBlogPost);
    $(document).on('click', '.viewBlogPost', viewBlogPost);
    $(document).on('click', '.deleteBlogPost', deleteBlogPost);
    $(document).on('click', '.publishBlogPost', publishBlogPost);
    $(document).on('click', '.draftBlogPost', draftBlogPost);

    $('#blogPostModal').on('show.bs.modal', function () {
        if (!$('#blogPostForm').attr('data-id')) {
            resetBlogPostForm();
        }
    });

    $('#blogPostModal').on('shown.bs.modal', function () {
        $('#title').trigger('focus');
    });

    $('#blogPostModal').on('hidden.bs.modal', resetBlogPostForm);

    $(document).on('focusin', function (event) {
        if ($(event.target).closest('.tox-tinymce-aux, .moxman-window, .tam-assetmanager-root').length) {
            event.stopImmediatePropagation();
        }
    });
});

function initRichEditor() {
    if (!window.tinymce || tinymce.get('blog_content')) {
        return;
    }

    tinymce.init({
        selector: '#blog_content',
        height: 320,
        menubar: false,
        branding: false,
        plugins: 'advlist autolink lists link charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime table wordcount',
        toolbar: 'undo redo | styleselect | bold italic underline removeformat | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | code fullscreen',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.7; }',
        setup: function (editor) {
            editor.on('change keyup undo redo', function () {
                editor.save();
            });
        }
    });
}

function submitBlogPostForm(event) {
    event.preventDefault();

    if (blogPostSubmitting) {
        return;
    }

    blogPostSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const editor = tinymce.get('blog_content');
    if (editor) {
        $('#blog_content').val(editor.getContent());
    }

    const formData = new FormData(document.getElementById('blogPostForm'));
    const postId = $('#blogPostForm').attr('data-id');
    let url = window.blogPostRoutes.index;

    if (postId) {
        url = `${window.blogPostRoutes.index}/${postId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#blogPostModal').modal('hide');
            tableBlogPost.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la publicacion. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            blogPostSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editBlogPost() {
    const postId = $(this).data('id');
    showLoading();

    $.get(`${window.blogPostRoutes.index}/${postId}`)
        .done(function (response) {
            prepareEditForm(response.post);
            $('#blogPostModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la publicacion.', 'error');
        })
        .always(hideLoading);
}

function viewBlogPost() {
    const postId = $(this).data('id');
    showLoading();

    $.get(`${window.blogPostRoutes.index}/${postId}`)
        .done(function (response) {
            fillDetailModal(response.post);
            $('#blogPostDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle de la publicacion.', 'error');
        })
        .always(hideLoading);
}

function deleteBlogPost() {
    const postId = $(this).data('id');
    const name = $(this).data('name') || 'esta publicacion';

    Swal.fire({
        title: 'Eliminar publicacion?',
        text: `Se eliminara "${name}".`,
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
            url: `${window.blogPostRoutes.index}/${postId}`,
            type: 'DELETE',
            success: function (response) {
                tableBlogPost.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la publicacion.', 'error');
            },
            complete: hideLoading
        });
    });
}

function publishBlogPost() {
    changePostStatus($(this).data('id'), $(this).data('name'), 'publish', 'Publicar publicacion?', 'Se mostrara en la pagina publica.');
}

function draftBlogPost() {
    changePostStatus($(this).data('id'), $(this).data('name'), 'draft', 'Pasar a borrador?', 'Dejara de mostrarse en la pagina publica.');
}

function changePostStatus(postId, name, action, title, text) {
    Swal.fire({
        title,
        text: `${text} "${name || 'Publicacion'}"`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        showLoading();

        $.post(`${window.blogPostRoutes.index}/${postId}/${action}`)
            .done(function (response) {
                tableBlogPost.ajax.reload(null, false);
                showToast(response.message);
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cambiar el estado.', 'error');
            })
            .always(hideLoading);
    });
}

function prepareEditForm(post) {
    resetBlogPostForm();

    $('#blogPostForm').attr('data-id', post.id);
    $('#blogPostModalLabel').text('Editar Publicacion');
    $('#saveBlogPostButton span').text('Actualizar Publicacion');
    $('#title').val(post.title || '');
    $('#summary').val(post.summary || '');
    $('#status').val(post.status || 'draft');
    $('#published_at').val(post.published_at || '');
    updateSlugPreview(post.slug);

    const editor = tinymce.get('blog_content');
    if (editor) {
        editor.setContent(post.content || '');
    } else {
        $('#blog_content').val(post.content || '');
    }

    setImagePreview(post.image_url || null, post.image_url ? 'Imagen actual' : null);
}

function fillDetailModal(post) {
    $('#detailBlogSubtitle').text(`Registro #${post.id}`);
    $('#detailBlogTitle').text(valueOrDash(post.title));
    $('#detailBlogSlug').text(valueOrDash(post.slug));
    $('#detailBlogStatus').html(statusBadge(post.status, post.status_label));
    $('#detailBlogAuthor').text(valueOrDash(post.author_name));
    $('#detailBlogPublishedAt').text(valueOrDash(post.published_at_formatted));
    $('#detailBlogCreatedAt').text(valueOrDash(post.created_at_formatted));
    $('#detailBlogUpdatedAt').text(valueOrDash(post.updated_at_formatted));
    $('#detailBlogSummary').text(valueOrDash(post.summary));
    $('#detailBlogContent').html(post.content || '');
    $('#detailPublicLink').toggleClass('d-none', !post.public_url).attr('href', post.public_url || '#');
    $('#detailBlogImage').toggleClass('d-none', !post.image_url).attr('src', post.image_url || '');
}

function resetBlogPostForm() {
    const form = document.getElementById('blogPostForm');

    if (!form) {
        return;
    }

    form.reset();
    $('#blogPostForm').removeAttr('data-id');
    $('#blogPostModalLabel').text('Nueva Publicacion');
    $('#saveBlogPostButton span').text('Guardar Publicacion');
    $('#status').val('draft');
    updateSlugPreview('');
    setImagePreview(null);
    clearValidation();

    const editor = tinymce.get('blog_content');
    if (editor) {
        editor.setContent('');
    } else {
        $('#blog_content').val('');
    }
}

function updateSlugPreview(forcedSlug = null) {
    const slug = forcedSlug !== null ? forcedSlug : slugify($('#title').val());
    $('#slugPreview').text(slug ? `Slug: ${slug}` : 'El slug se generara automaticamente.');
}

function slugify(value) {
    return String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function previewSelectedImage() {
    const file = this.files && this.files[0];

    if (!file) {
        return;
    }

    const objectUrl = URL.createObjectURL(file);
    setImagePreview(objectUrl, file.name, true);
    $('#image_file-error').text('');
}

function setImagePreview(url, fileName = null, isObjectUrl = false) {
    if (blogImageObjectUrl) {
        URL.revokeObjectURL(blogImageObjectUrl);
        blogImageObjectUrl = null;
    }

    if (isObjectUrl) {
        blogImageObjectUrl = url;
    }

    $('#imageFileName').text(fileName || 'Ningun archivo seleccionado');

    if (!url) {
        $('#blogImagePreview').attr('src', '').addClass('d-none');
        $('#blogImagePlaceholder').removeClass('d-none');
        return;
    }

    $('#blogImagePreview').attr('src', url).removeClass('d-none');
    $('#blogImagePlaceholder').addClass('d-none');
}

function clearValidation() {
    $('#blog-post-error-messages').addClass('d-none').empty();
    $('#blogPostForm .is-invalid').removeClass('is-invalid');
    $('#blogPostForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        const inputId = field === 'content' ? 'blog_content' : field;
        $(`#${inputId}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#blog-post-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function statusBadge(status, label) {
    const classes = {
        draft: 'badge-warning',
        published: 'badge-success'
    };

    return `<span class="badge ${classes[status] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveBlogPostButton');
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
