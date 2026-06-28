<div class="modal fade" id="cattleSaleDetailModal" tabindex="-1" role="dialog" aria-labelledby="cattleSaleDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-cash-register text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="cattleSaleDetailModalLabel">Detalle de Venta</h5>
                        <small class="text-muted" id="detailSaleSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="sale-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="sale-detail-photo-wrap mx-auto">
                                <img id="detailSaleCattlePhoto" class="sale-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailSaleCattlePhotoPlaceholder" class="sale-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="sale-detail-label">Ganado</div>
                            <div class="sale-detail-value h4 mb-0" id="detailSaleCattle">-</div>
                            <div class="text-muted mt-1" id="detailSaleBreed">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailSaleStatusBadge">-</div>
                            <div class="mt-2" id="detailSaleCattleStatus">-</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="sale-section-title"><i class="fas fa-handshake"></i> Operacion</div>
                        <div class="sale-detail-grid">
                            <div class="sale-detail-item"><div class="sale-detail-label">Vendedor</div><div class="sale-detail-value" id="detailSeller">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Comprador</div><div class="sale-detail-value" id="detailBuyer">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Fecha</div><div class="sale-detail-value" id="detailSaleDate">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Precio</div><div class="sale-detail-value" id="detailSalePrice">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Metodo de pago</div><div class="sale-detail-value" id="detailPaymentMethod">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Contrato</div><div class="sale-detail-value" id="detailContract">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="sale-section-title"><i class="fas fa-clipboard-list"></i> Registro</div>
                        <div class="sale-detail-grid">
                            <div class="sale-detail-item sale-detail-item-wide"><div class="sale-detail-label">Observaciones</div><div class="sale-detail-value" id="detailSaleNotes">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Fecha de registro</div><div class="sale-detail-value" id="detailSaleCreatedAt">-</div></div>
                            <div class="sale-detail-item"><div class="sale-detail-label">Ultima actualizacion</div><div class="sale-detail-value" id="detailSaleUpdatedAt">-</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
