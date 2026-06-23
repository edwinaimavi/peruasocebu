<div class="modal fade" id="purchaseViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">

            <!-- HEADER -->
            <div class="modal-header bg-white border-bottom">
                <div>
                    <h5 class="modal-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-eye text-success mr-2"></i>
                        Detalle de la Compra
                    </h5>
                    <small class="text-muted">Vista completa de la compra</small>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- INFO GENERAL -->
                <div class="row mb-3 text-muted small">

                    <div class="col-md-3">
                        <i class="fas fa-truck mr-1"></i>
                        <strong>Proveedor:</strong>
                        <div id="viewSupplier"></div>
                    </div>

                    <div class="col-md-2">
                        <i class="fas fa-file-alt mr-1"></i>
                        <strong>Tipo Doc:</strong>
                        <div id="viewDocumentType"></div>
                    </div>

                    <div class="col-md-2">
                        <i class="fas fa-hashtag mr-1"></i>
                        <strong>N° Doc:</strong>
                        <div id="viewDocumentNumber"></div>
                    </div>

                    <div class="col-md-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <strong>Fecha:</strong>
                        <div id="viewDate"></div>
                    </div>

                    <div class="col-md-3">
                        <i class="fas fa-check-circle mr-1"></i>
                        <strong>Estado:</strong>
                        <div id="viewStatus"></div>
                    </div>

                </div>

                <!-- DETALLE -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light font-weight-bold">
                        <i class="fas fa-boxes mr-1"></i>
                        Detalle de Productos
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th>Producto</th>
                                    <th width="10%">Cantidad</th>
                                    <th width="15%">Costo</th>
                                    <th width="15%">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody id="viewPurchaseDetails"></tbody>
                        </table>
                    </div>
                </div>

                <!-- TOTAL -->
                <div class="text-right">
                    <h4 class="font-weight-bold text-success">
                        Total: <span id="viewTotal">S/ 0.00</span>
                    </h4>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>