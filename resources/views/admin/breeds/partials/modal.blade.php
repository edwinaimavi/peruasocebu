<div class="modal fade" id="breedModal" tabindex="-1" role="dialog" aria-labelledby="breedModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-dna text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="breedModalLabel">Nueva Raza</h5>
                        <small class="text-muted">Clasificación genética y características productivas</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="breedForm" class="breed-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="breed-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label class="small font-weight-bold text-secondary" for="name">
                                        Nombre de la raza <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm" id="name" name="name"
                                        type="text" maxlength="255" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="code">
                                        Código <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm text-uppercase" id="code"
                                        name="code" type="text" maxlength="30"
                                        placeholder="Se generará automáticamente" readonly>
                                    <small class="form-text text-muted">
                                        El código se genera automáticamente según el nombre de la raza.
                                    </small>
                                    <div class="invalid-feedback" id="code-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="origin_country">
                                        País de origen
                                    </label>
                                    <input class="form-control form-control-sm" id="origin_country"
                                        name="origin_country" type="text" maxlength="150">
                                    <div class="invalid-feedback" id="origin_country-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="status">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="status" name="status" required>
                                        <option value="active">Activo</option>
                                        <option value="inactive">Inactivo</option>
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary" for="breed_image">
                                    Imagen de la raza
                                </label>
                                <div class="breed-image-upload-card">
                                    <div class="breed-image-preview" id="breedImagePreview">
                                        <i class="fas fa-cow"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                    <div class="breed-image-upload-actions">
                                        <label class="btn btn-light border btn-sm mb-1" for="breed_image">
                                            <i class="fas fa-image mr-1"></i> Seleccionar imagen
                                        </label>
                                        <small class="text-muted d-block">JPG, PNG o WEBP. Maximo 4 MB.</small>
                                    </div>
                                </div>
                                <input class="d-none" id="breed_image" name="image" type="file"
                                    accept="image/jpeg,image/png,image/webp">
                                <div class="invalid-feedback d-block" id="image-error"></div>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary"
                                    for="breed_description">Descripción</label>
                                <textarea class="form-control form-control-sm" id="breed_description" name="description"
                                    rows="4" placeholder="Origen, propósito productivo o descripción general"></textarea>
                                <div class="invalid-feedback" id="description-error"></div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary"
                                    for="breed_characteristics">Características</label>
                                <textarea class="form-control form-control-sm" id="breed_characteristics" name="characteristics"
                                    rows="4" placeholder="Características físicas, productivas o reproductivas"></textarea>
                                <div class="invalid-feedback" id="characteristics-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveBreedButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Raza</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
