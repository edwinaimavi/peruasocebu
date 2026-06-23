<!-- Modal Supplier -->
<div class="modal fade" id="supplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">

                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-truck text-success"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Nuevo Proveedor</h5>
                        <small class="text-muted">
                            Gestiona los proveedores del sistema
                        </small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="supplierForm">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <div class="row">

                                <!-- LEFT -->
                                <div class="col-lg-12">

                                    <!-- Nombre -->
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-secondary">
                                            NOMBRE *
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control form-control-sm">
                                        <div class="invalid-feedback" id="name-error"></div>
                                    </div>

                                    <div class="form-row">

                                        <!-- RUC -->
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary">
                                                RUC
                                            </label>
                                            <input type="text" name="ruc" id="ruc"
                                                class="form-control form-control-sm">
                                            <div class="invalid-feedback" id="ruc-error"></div>
                                        </div>

                                        <!-- TELÉFONO -->
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary">
                                                TELÉFONO
                                            </label>
                                            <input type="text" name="phone" id="phone"
                                                class="form-control form-control-sm">
                                            <div class="invalid-feedback" id="phone-error"></div>
                                        </div>

                                        <!-- EMAIL -->
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary">
                                                EMAIL
                                            </label>
                                            <input type="email" name="email" id="email"
                                                class="form-control form-control-sm">
                                            <div class="invalid-feedback" id="email-error"></div>
                                        </div>

                                    </div>

                                    <!-- DIRECCIÓN -->
                                    <div class="form-group">
                                        <label class="small font-weight-bold text-secondary">
                                            DIRECCIÓN
                                        </label>
                                        <input type="text" name="address" id="address"
                                            class="form-control form-control-sm">
                                        <div class="invalid-feedback" id="address-error"></div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="form-group">
                                        <label class="small d-block">ESTADO</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="status"
                                                name="status" value="1" checked>
                                            <label class="custom-control-label" for="status">
                                                Activo
                                            </label>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-success">
                            Guardar Proveedor
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
