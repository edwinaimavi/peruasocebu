<!-- Modal Brand -->
<div class="modal fade" id="brandModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">

                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-tags text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Nueva Marca</h5>
                        <small class="text-muted">
                            Gestiona las marcas de tus productos
                        </small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="brandForm" enctype="multipart/form-data">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <div class="row">

                                <!-- LEFT -->
                                <div class="col-lg-8">

                                    <!-- Nombre + slug -->
                                    <div class="form-row">
                                        <div class="form-group col-md-8">
                                            <label class="small font-weight-bold text-secondary">
                                                NOMBRE *
                                            </label>
                                            <input type="text" name="name" id="name"
                                                class="form-control form-control-sm">
                                            <div class="invalid-feedback" id="name-error"></div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary">
                                                SLUG *
                                            </label>
                                            <input type="text" name="slug" id="slug"
                                                class="form-control form-control-sm" readonly>
                                            <div class="invalid-feedback" id="slug-error"></div>
                                        </div>
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

                                <!-- RIGHT (IMAGEN) -->
                                <div class="col-lg-4">

                                    <div class="image-uploader">

                                        <!-- INPUT OCULTO -->
                                        <input type="file" id="image" name="image" accept="image/*" hidden>

                                        <!-- DROP ZONE -->
                                        <div id="uploadBox"
                                            class="upload-box-modern"
                                            data-width="500"
                                            data-height="400">

                                            <div id="uploadPlaceholder" class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Arrastra o haz clic para subir</p>
                                                <small>
                                                    PNG, JPG (recomendado <span id="imgSizeText">500x400</span>)
                                                </small>
                                            </div>

                                            <!-- PREVIEW -->
                                            <div id="imagePreview" class="d-none">
                                                <img id="previewImg" src="">
                                                <button type="button" id="removeImage" class="btn-remove">
                                                    ✕
                                                </button>
                                            </div>

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

                        <button type="submit" class="btn btn-primary">
                            Guardar Marca
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<!-- MODAL CROP -->
<div class="modal fade" id="cropModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Ajustar imagen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body text-center">
                <img id="imageToCrop" style="max-width:100%;">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" id="cropImageBtn">Cortar y usar</button>
            </div>

        </div>
    </div>
</div>