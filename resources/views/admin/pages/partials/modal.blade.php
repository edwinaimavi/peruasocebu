<!-- Modal elegante para Page (Bootstrap 5) -->
<div class="modal fade" id="pageModal" tabindex="-1" aria-labelledby="pageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-light rounded-circle p-2">
                        <i class="fas fa-file-alt text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="pageModalLabel">Nueva Página</h5>
                        <small class="text-muted">
                            Crea páginas fijas como Nosotros, Contacto o Términos
                        </small>
                    </div>
                </div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body bg-light">
                <form id="pageForm" autocomplete="off" class="row g-3">
                    @csrf

                    <!-- INFO LATERAL -->
                    <div class="col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm h-100">
                            <div class="card-body text-center">

                                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>

                                <h6 class="fw-bold mb-1">
                                    Página informativa
                                </h6>

                                <small class="text-muted d-block">
                                    Ideal para contenido estático
                                </small>

                                <hr>

                                <small class="text-muted d-block">
                                    <i class="far fa-clock me-1"></i>
                                    Visible en el sitio web
                                </small>

                            </div>
                        </div>
                    </div>

                    <!-- FORM PRINCIPAL -->
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-4 shadow-sm">
                            <div class="card-body">

                                <!-- Título + slug -->
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label fw-bold text-secondary small">
                                            TÍTULO <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="title" id="title"
                                            class="form-control form-control-sm"
                                            placeholder="Ej: Nosotros, Contacto, Términos y Condiciones">
                                        <div class="invalid-feedback" id="title-error"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-secondary small">
                                            SLUG <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="slug" id="slug"
                                            class="form-control form-control-sm" placeholder="nosotros" readonly>
                                        <div class="invalid-feedback" id="slug-error"></div>
                                    </div>
                                </div>

                                <!-- ESTADO -->
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary d-block">
                                        ESTADO DE LA PAGINA
                                    </label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="status"
                                            name="status" value="published">
                                        <label class="custom-control-label" for="status">
                                            Publicado
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="col-12">
                        <div class="card border-0 rounded-4 shadow-sm">
                            <div class="card-body">

                                <label class="form-label fw-bold text-secondary small">
                                    CONTENIDO <span class="text-danger">*</span>
                                </label>

                                <textarea name="content" id="content" rows="8" class="form-control form-control-sm"
                                    placeholder="Escribe el contenido de la página..."></textarea>

                                <div class="invalid-feedback d-block" id="content-error"></div>

                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Cancelar
                        </button>

                        <button type="submit" id="btnSavePage" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Guardar Página
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
