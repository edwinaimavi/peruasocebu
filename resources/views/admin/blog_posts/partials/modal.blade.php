<div class="modal fade" id="blogPostModal" tabindex="-1" role="dialog" aria-labelledby="blogPostModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-newspaper text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="blogPostModalLabel">Nueva Publicacion</h5>
                        <small class="text-muted">Contenido publico del blog</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="blogPostForm" class="blog-post-modal-form" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body p-3">
                    @csrf
                    <div id="blog-post-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="blog-section-title"><i class="fas fa-heading"></i> Datos principales</div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="title">Titulo <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="title" name="title" type="text" maxlength="255" required>
                                    <small class="form-text text-muted" id="slugPreview">El slug se generara automaticamente.</small>
                                    <div class="invalid-feedback" id="title-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="status">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="status" name="status" required>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="published_at">Fecha de publicacion</label>
                                    <input class="form-control form-control-sm" id="published_at" name="published_at" type="datetime-local">
                                    <div class="invalid-feedback" id="published_at-error"></div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="summary">Resumen</label>
                                <textarea class="form-control form-control-sm" id="summary" name="summary" rows="3" maxlength="500"></textarea>
                                <div class="invalid-feedback" id="summary-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="blog-section-title"><i class="fas fa-align-left"></i> Contenido</div>
                            <textarea class="form-control" id="blog_content" name="content"></textarea>
                            <div class="invalid-feedback d-block" id="content-error"></div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="blog-section-title"><i class="fas fa-image"></i> Imagen principal</div>
                            <div class="blog-upload-card">
                                <div class="blog-image-preview">
                                    <img id="blogImagePreview" class="d-none" src="" alt="Vista previa">
                                    <span id="blogImagePlaceholder" class="text-muted small">Sin imagen</span>
                                </div>
                                <div class="flex-fill">
                                    <div class="font-weight-bold text-success">Portada de la publicacion</div>
                                    <div class="text-muted small">Imagen JPG, PNG o WEBP - Max. 4 MB</div>
                                    <input class="d-none" id="image_file" name="image_file" type="file" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="mt-2">
                                        <label for="image_file" class="btn btn-photo-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar imagen
                                        </label>
                                    </div>
                                    <div class="text-muted small mt-2" id="imageFileName">Ningun archivo seleccionado</div>
                                    <div class="invalid-feedback d-block" id="image_file-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBlogPostButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Publicacion</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
