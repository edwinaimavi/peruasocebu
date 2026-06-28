var divLoading = document.getElementById('divLoading');
let tableContactMessage;
let selectedContactMessageId = null;
let selectedStatus = '';

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableContactMessage = $('#tableContactMessage').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.contactMessageRoutes.list,
            data: function (data) {
                data.status = selectedStatus;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'full_name', name: 'full_name' },
            { data: 'phone', name: 'phone', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'email', name: 'email', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'subject', name: 'subject', defaultContent: 'Sin asunto', render: $.fn.dataTable.render.text() },
            { data: 'status', name: 'status', orderable: false },
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

    $('.btn-filter').on('click', function () {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        selectedStatus = $(this).data('status') || '';
        tableContactMessage.ajax.reload();
    });

    $(document).on('click', '.viewContactMessage', viewContactMessage);
    $(document).on('click', '.markContactRead', function () {
        changeContactStatus($(this).data('id'), 'mark-read');
    });
    $(document).on('click', '.markContactAnswered', function () {
        changeContactStatus($(this).data('id'), 'mark-answered');
    });
    $(document).on('click', '.markContactNew', function () {
        changeContactStatus($(this).data('id'), 'mark-new');
    });
    $(document).on('click', '.deleteContactMessage', deleteContactMessage);
    $(document).on('click', '#detailDeleteContactMessage', function () {
        if (!selectedContactMessageId) {
            return;
        }

        deleteContactMessageById(selectedContactMessageId, $('#detailContactName').text());
    });
    $(document).on('click', '.detailStatusAction', function () {
        if (selectedContactMessageId) {
            changeContactStatus(selectedContactMessageId, $(this).data('action'), true);
        }
    });
    $(document).on('click', '.copyContactData', copyContactData);
});

function viewContactMessage() {
    const messageId = $(this).data('id');
    selectedContactMessageId = messageId;
    showLoading();

    $.get(`${window.contactMessageRoutes.index}/${messageId}`)
        .done(function (response) {
            fillDetailModal(response.message);
            tableContactMessage.ajax.reload(null, false);
            $('#contactMessageDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle del mensaje.', 'error');
        })
        .always(hideLoading);
}

function changeContactStatus(messageId, action, refreshDetail = false) {
    const labels = {
        'mark-read': 'marcar como leido',
        'mark-answered': 'marcar como respondido',
        'mark-new': 'marcar como nuevo'
    };

    Swal.fire({
        title: 'Cambiar estado?',
        text: `Se va a ${labels[action] || 'cambiar el estado'} este mensaje.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        showLoading();

        $.post(`${window.contactMessageRoutes.index}/${messageId}/${action}`)
            .done(function (response) {
                tableContactMessage.ajax.reload(null, false);

                if (refreshDetail && response.contactMessage) {
                    fillDetailModal(response.contactMessage);
                }

                showToast(response.message);
            })
            .fail(function () {
                Swal.fire('Error', 'No se pudo cambiar el estado del mensaje.', 'error');
            })
            .always(hideLoading);
    });
}

function deleteContactMessage() {
    const messageId = $(this).data('id');
    const name = $(this).data('name') || 'este contacto';

    deleteContactMessageById(messageId, name);
}

function deleteContactMessageById(messageId, name) {
    Swal.fire({
        title: 'Eliminar mensaje?',
        text: `Se eliminara el mensaje de "${name}".`,
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
            url: `${window.contactMessageRoutes.index}/${messageId}`,
            type: 'DELETE',
            success: function (response) {
                if (selectedContactMessageId === messageId) {
                    $('#contactMessageDetailModal').modal('hide');
                    selectedContactMessageId = null;
                }

                tableContactMessage.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar el mensaje.', 'error');
            },
            complete: hideLoading
        });
    });
}

function fillDetailModal(message) {
    selectedContactMessageId = message.id;
    $('#detailContactSubtitle').text(`Mensaje #${message.id}`);
    $('#detailContactName').text(valueOrDash(message.full_name));
    $('#detailContactSubject').text(valueOrDash(message.subject_label));
    $('#detailContactStatus').html(message.status_badge || statusBadge(message.status, message.status_label));
    $('#detailContactPhone').text(valueOrDash(message.phone_label));
    $('#detailContactEmailText').text(valueOrDash(message.email_label));
    $('#detailContactCreatedAt').text(valueOrDash(message.created_at_formatted));
    $('#detailContactUpdatedAt').text(valueOrDash(message.updated_at_formatted));
    $('#detailContactMessage').text(valueOrDash(message.message));

    $('#detailContactWhatsapp')
        .toggleClass('d-none', !message.whatsapp_url)
        .attr('href', message.whatsapp_url || '#');

    $('#detailContactEmail')
        .toggleClass('d-none', !message.mailto_url)
        .attr('href', message.mailto_url || '#');

    $('.copyContactData[data-copy-target="#detailContactPhone"]').toggleClass('d-none', !message.phone);
    $('.copyContactData[data-copy-target="#detailContactEmailText"]').toggleClass('d-none', !message.email);
}

function copyContactData() {
    const target = $(this).data('copy-target');
    const label = $(this).data('copy-label') || 'Dato';
    const value = $(target).text();

    if (!value || value === '-') {
        Swal.fire('Atencion', `No hay ${label.toLowerCase()} para copiar.`, 'warning');
        return;
    }

    navigator.clipboard.writeText(value)
        .then(function () {
            showToast(`${label} copiado correctamente.`);
        })
        .catch(function () {
            Swal.fire('Atencion', 'No se pudo copiar automaticamente.', 'warning');
        });
}

function statusBadge(status, label) {
    const classes = {
        new: 'badge-primary',
        read: 'badge-secondary',
        answered: 'badge-success'
    };

    return `<span class="badge ${classes[status] || 'badge-light'} px-2 py-1">${escapeHtml(label || '-')}</span>`;
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
