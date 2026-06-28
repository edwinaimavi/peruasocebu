var divLoading = document.getElementById('divLoading');
let tableCattleSale;
let cattleSaleSubmitting = false;
let currentContractUrl = null;

document.addEventListener('DOMContentLoaded', function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    tableCattleSale = $('#tableCattleSale').DataTable({
        processing: true,
        serverSide: true,
        ajax: window.cattleSaleRoutes.list,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'cattle_name', name: 'cattle.name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'cattle_code', name: 'cattle.code', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'seller_name', name: 'seller.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'buyer_name', name: 'buyer.full_name', defaultContent: '-', render: $.fn.dataTable.render.text() },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'sale_price', name: 'sale_price', render: $.fn.dataTable.render.text() },
            { data: 'payment_method', name: 'payment_method', orderable: false },
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

    $('#cattle_id').on('change', fillSellerFromSelectedCattle);
    $('#contract_file').on('change', function () {
        const file = this.files && this.files[0];
        $('#contractFileName').text(file ? file.name : 'Ningun archivo seleccionado');
        if (file) {
            $('#contract_file-error').text('');
        }
    });
    $('#cattleSaleForm').on('submit', submitCattleSaleForm);

    $(document).on('click', '.editCattleSale', editCattleSale);
    $(document).on('click', '.viewCattleSale', viewCattleSale);
    $(document).on('click', '.deleteCattleSale', deleteCattleSale);

    $('#cattleSaleModal').on('show.bs.modal', function () {
        if (!$('#cattleSaleForm').attr('data-id')) {
            resetCattleSaleForm();
        }
    });

    $('#cattleSaleModal').on('shown.bs.modal', function () {
        $('#cattle_id').trigger('focus');
    });

    $('#cattleSaleModal').on('hidden.bs.modal', resetCattleSaleForm);
});

function submitCattleSaleForm(event) {
    event.preventDefault();

    if (cattleSaleSubmitting) {
        return;
    }

    cattleSaleSubmitting = true;
    clearValidation();
    setSaveButtonLoading(true);
    showLoading();

    const form = event.currentTarget;
    const saleId = $('#cattleSaleForm').attr('data-id');
    const formData = new FormData(form);
    let url = window.cattleSaleRoutes.index;

    if (saleId) {
        url = `${window.cattleSaleRoutes.index}/${saleId}`;
        formData.append('_method', 'PUT');
    }

    $.ajax({
        url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            $('#cattleSaleModal').modal('hide');
            tableCattleSale.ajax.reload(null, false);
            showToast(response.message);
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                showValidationErrors(xhr.responseJSON.errors || {});
                return;
            }

            Swal.fire('Error', 'No se pudo guardar la venta. Intentelo nuevamente.', 'error');
        },
        complete: function () {
            cattleSaleSubmitting = false;
            setSaveButtonLoading(false);
            hideLoading();
        }
    });
}

function editCattleSale() {
    const saleId = $(this).data('id');
    showLoading();

    $.get(`${window.cattleSaleRoutes.index}/${saleId}`)
        .done(function (response) {
            prepareEditForm(response.sale);
            $('#cattleSaleModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar la venta.', 'error');
        })
        .always(hideLoading);
}

function viewCattleSale() {
    const saleId = $(this).data('id');
    showLoading();

    $.get(`${window.cattleSaleRoutes.index}/${saleId}`)
        .done(function (response) {
            fillDetailModal(response.sale);
            $('#cattleSaleDetailModal').modal('show');
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudo cargar el detalle de venta.', 'error');
        })
        .always(hideLoading);
}

function deleteCattleSale() {
    const saleId = $(this).data('id');
    const saleName = $(this).data('name') || 'esta venta';

    Swal.fire({
        title: 'Eliminar venta?',
        text: `Se eliminara "${saleName}".`,
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
            url: `${window.cattleSaleRoutes.index}/${saleId}`,
            type: 'DELETE',
            success: function (response) {
                tableCattleSale.ajax.reload(null, false);
                showToast(response.message);
            },
            error: function () {
                Swal.fire('Error', 'No se pudo eliminar la venta.', 'error');
            },
            complete: hideLoading
        });
    });
}

function prepareEditForm(sale) {
    resetCattleSaleForm();

    $('#cattleSaleForm').attr('data-id', sale.id);
    $('#cattleSaleModalLabel').text('Editar Venta');
    $('#saveCattleSaleButton span').text('Actualizar Venta');

    [
        'cattle_id', 'seller_owner_id', 'buyer_owner_id', 'sale_date',
        'sale_price', 'currency', 'payment_method', 'status', 'notes'
    ].forEach(function (field) {
        $(`#${field}`).val(sale[field] ?? '');
    });

    currentContractUrl = sale.contract_file_url || null;
    $('#currentContractLink')
        .toggleClass('d-none', !currentContractUrl)
        .attr('href', currentContractUrl || '#');
    $('#contractFileName').text(sale.contract_file_name || 'Ningun archivo seleccionado');
    $('#sellerHelp').addClass('d-none');
}

function fillDetailModal(sale) {
    $('#detailSaleSubtitle').text(`Registro #${sale.id}`);
    $('#detailSaleCattle').text(valueOrDash(sale.cattle_label));
    $('#detailSaleBreed').text(sale.cattle_breed_name ? `Raza: ${sale.cattle_breed_name}` : '-');
    $('#detailSeller').text(valueOrDash(sale.seller_name));
    $('#detailBuyer').text(valueOrDash(sale.buyer_name));
    $('#detailSaleDate').text(valueOrDash(sale.sale_date_formatted));
    $('#detailSalePrice').text(valueOrDash(sale.sale_price_formatted));
    $('#detailPaymentMethod').text(valueOrDash(sale.payment_method_label));
    $('#detailSaleNotes').text(valueOrDash(sale.notes));
    $('#detailSaleCreatedAt').text(valueOrDash(sale.created_at_formatted));
    $('#detailSaleUpdatedAt').text(valueOrDash(sale.updated_at_formatted));
    $('#detailSaleStatusBadge').html(statusBadge(sale.status, sale.status_label));
    $('#detailSaleCattleStatus').html(`<span class="badge badge-light border px-3 py-2">${escapeHtml(sale.cattle_sale_status_label || '-')}</span>`);
    $('#detailContract').html(sale.contract_file_url
        ? `<a class="btn btn-outline-primary btn-sm" href="${escapeHtml(sale.contract_file_url)}" target="_blank" rel="noopener"><i class="fas fa-download mr-1"></i> Ver contrato</a>`
        : '<span class="text-muted">Sin contrato adjunto.</span>');
    setCattlePhoto(sale.cattle_photo_url || null);
}

function fillSellerFromSelectedCattle() {
    const cattleId = Number($('#cattle_id').val());
    const cattle = (window.cattleSaleCattle || []).find(function (item) {
        return Number(item.id) === cattleId;
    });

    if (!cattle) {
        $('#seller_owner_id').val('');
        $('#sellerHelp').addClass('d-none');
        return;
    }

    $('#seller_owner_id').val(cattle.current_owner_id || '');
    $('#sellerHelp').toggleClass('d-none', Boolean(cattle.current_owner_id));
}

function resetCattleSaleForm() {
    const form = document.getElementById('cattleSaleForm');
    if (!form) {
        return;
    }

    form.reset();
    $('#cattleSaleForm').removeAttr('data-id');
    $('#cattle_id').val(defaultCattleIdFromUrl()).trigger('change');
    $('#currency').val('PEN');
    $('#status').val('pending');
    $('#cattleSaleModalLabel').text('Nueva Venta');
    $('#saveCattleSaleButton span').text('Guardar Venta');
    $('#currentContractLink').addClass('d-none').attr('href', '#');
    $('#contractFileName').text('Ningun archivo seleccionado');
    $('#sellerHelp').addClass('d-none');
    currentContractUrl = null;
    clearValidation();
}

function defaultCattleIdFromUrl() {
    const params = new URLSearchParams(window.location.search);

    return params.get('cattle_id') || '';
}

function clearValidation() {
    $('#cattle-sale-error-messages').addClass('d-none').empty();
    $('#cattleSaleForm .is-invalid').removeClass('is-invalid');
    $('#cattleSaleForm .invalid-feedback').text('');
}

function showValidationErrors(errors) {
    const messages = [];

    Object.entries(errors).forEach(function ([field, fieldMessages]) {
        messages.push(fieldMessages[0]);
        $(`#${field}`).addClass('is-invalid');
        $(`#${field}-error`).text(fieldMessages[0]);
    });

    $('#cattle-sale-error-messages')
        .removeClass('d-none')
        .html(`<strong>Revise los datos ingresados:</strong><ul class="mb-0 mt-2">${messages.map(
            message => `<li>${escapeHtml(message)}</li>`
        ).join('')}</ul>`);
}

function statusBadge(status, label) {
    const classes = {
        pending: 'badge-warning',
        completed: 'badge-success',
        cancelled: 'badge-danger'
    };

    return `<span class="badge ${classes[status] || 'badge-secondary'} px-3 py-2">${escapeHtml(label || '-')}</span>`;
}

function setCattlePhoto(url) {
    const $photo = $('#detailSaleCattlePhoto');
    const $placeholder = $('#detailSaleCattlePhotoPlaceholder');

    if (!url) {
        $photo.attr('src', '').addClass('d-none');
        $placeholder.removeClass('d-none');
        return;
    }

    $photo.attr('src', url).removeClass('d-none');
    $placeholder.addClass('d-none');
}

function setSaveButtonLoading(isLoading) {
    const $button = $('#saveCattleSaleButton');
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
