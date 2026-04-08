<!-- Modal Categoría -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg">

            <!-- Header -->
            <div class="modal-header bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div class="icon-circle mr-2">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h5 class="modal-title mb-0" id="categoryModalLabel">
                        Nueva Categoría
                    </h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Form -->
            <form id="categoryForm" autocomplete="off">
                <div class="modal-body">

                    <div class="row">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm"
                                    placeholder="Ej: Electrónica">
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    Slug <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="slug" id="slug" class="form-control form-control-sm"
                                    placeholder="electronica" readonly>
                                <div class="invalid-feedback" id="slug-error"></div>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    Descripción
                                </label>
                                <textarea name="description" id="description" class="form-control form-control-sm" rows="3"
                                    placeholder="Descripción opcional de la categoría..."></textarea>
                                <div class="invalid-feedback" id="description-error"></div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-12">
                            <div class="form-group d-flex align-items-center justify-content-between mt-2">
                                <label class="form-label mb-0">
                                    Estado
                                </label>

                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status"
                                        value="1" checked>
                                    <label class="custom-control-label" for="status">
                                        Activo
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i> Guardar Categoría
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
