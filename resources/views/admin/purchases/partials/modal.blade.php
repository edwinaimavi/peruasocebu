<div class="modal fade" id="purchaseModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            ```
            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">

                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-file-invoice-dollar text-success"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="purchaseModalLabel">Nueva Compra</h5>
                        <small class="text-muted">Registro de compras a proveedores</small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="purchaseForm">

                    <!-- ========================= -->
                    <!-- CABECERA -->
                    <!-- ========================= -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary">PROVEEDOR *</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control form-control-sm">
                                        <option value="">Seleccione</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary">TIPO DOC *</label>
                                    <select name="document_type" id="document_type"
                                        class="form-control form-control-sm">
                                        <option value="">Seleccione</option>
                                        <option value="factura">Factura</option>
                                        <option value="boleta">Boleta</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary">N° DOCUMENTO *</label>
                                    <input type="text" name="document_number" id="document_number"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">FECHA *</label>
                                    <input type="date" name="date" id="date"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="form-group col-md-4">
                                    <label class="small d-block">ESTADO</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status"
                                            name="status" value="1" checked>
                                        <label class="custom-control-label" for="status">Activo</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ========================= -->
                    <!-- DETALLE DE COMPRA -->
                    <!-- ========================= -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 font-weight-bold">
                                <i class="fas fa-boxes text-primary mr-1"></i>
                                Detalle de Compra
                            </h6>

                            <button type="button" id="addRow" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Agregar Producto
                            </button>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0 text-center" id="purchaseDetailTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Producto</th>
                                            <th width="120">Cantidad</th>
                                            <th width="150">Costo</th>
                                            <th width="150">Subtotal</th>
                                            <th width="60"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="purchaseDetailBody">
                                        <!-- FILAS DINÁMICAS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <h5 class="mb-0">
                                Total: <span id="totalPurchase">S/ 0.00</span>
                            </h5>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success">
                            Guardar Compra
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
    ```

</div>
