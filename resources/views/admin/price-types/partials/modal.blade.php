<!-- Modal Price Type -->
<div class="modal fade" id="priceTypeModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">

                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-dollar-sign text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="priceTypeModalLabel">
                            Nuevo Tipo de Precio
                        </h5>
                        <small class="text-muted">
                            Gestiona los tipos de precios del sistema
                        </small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="priceTypeForm">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <!-- Nombre -->
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary">
                                    NOMBRE *
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-sm">
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>

                            <!-- Código -->
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary">
                                    CÓDIGO *
                                </label>
                                <input type="text" name="code" id="code"
                                    class="form-control form-control-sm"
                                    placeholder="Ej: Tecnología, Público, Mayorista">
                                <div class="invalid-feedback" id="code-error"></div>
                            </div>

                            <!-- Estado -->
                            <div class="form-group">
                                <label class="small d-block">ESTADO</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input"
                                        id="status" name="status" value="1" checked>
                                    <label class="custom-control-label" for="status">
                                        Activo
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Guardar Tipo de Precio
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>