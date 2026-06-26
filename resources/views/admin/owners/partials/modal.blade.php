<div class="modal fade" id="ownerModal" tabindex="-1" role="dialog" aria-labelledby="ownerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-user-tie text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ownerModalLabel">Nuevo Propietario</h5>
                        <small class="text-muted">Información personal o empresarial · campos obligatorios (*)</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="ownerForm" class="owner-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="owner-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="owner-photo-card mb-3">
                                <div class="owner-photo-preview-wrap">
                                    <img id="photoPreview" class="owner-photo-preview d-none" src=""
                                        alt="Foto del propietario">
                                    <div id="photoPlaceholder" class="owner-photo-placeholder">
                                        <i class="fas fa-user"></i>
                                        <span>Sin foto</span>
                                    </div>
                                </div>
                                <div class="owner-photo-controls">
                                    <div class="owner-photo-title">
                                        <i class="fas fa-camera mr-1"></i>
                                        Foto del propietario
                                    </div>
                                    <div class="owner-photo-subtitle">JPG, PNG o WEBP · Máx. 4 MB</div>
                                    <input class="d-none" id="photo" name="photo" type="file"
                                        accept="image/jpeg,image/png,image/webp">
                                    <div class="owner-photo-actions">
                                        <label for="photo" class="btn btn-photo-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar foto
                                        </label>
                                        <button class="btn btn-photo-remove d-none" id="btnRemovePhotoPreview"
                                            type="button">
                                            <i class="fas fa-times mr-1"></i> Quitar
                                        </button>
                                    </div>
                                    <div class="owner-photo-filename" id="photoFileName">Ningún archivo seleccionado</div>
                                    <div class="invalid-feedback d-block" id="photo-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="owner_type">
                                        Tipo de propietario <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="owner_type" name="owner_type"
                                        required>
                                        <option value="person">Persona</option>
                                        <option value="company">Empresa</option>
                                    </select>
                                    <div class="invalid-feedback" id="owner_type-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="document_type">
                                        Tipo de documento
                                    </label>
                                    <select class="form-control form-control-sm" id="document_type"
                                        name="document_type">
                                        <option value="">Seleccione</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>
                                        <option value="CE">CE</option>
                                        <option value="PASSPORT">Pasaporte</option>
                                        <option value="OTHER">Otro</option>
                                    </select>
                                    <div class="invalid-feedback" id="document_type-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="document_number">
                                        Número de documento
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" id="document_number" name="document_number"
                                            type="text" maxlength="30" inputmode="numeric">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" id="btnSearchDocument"
                                                type="button">
                                                <i class="fas fa-search mr-1"></i>
                                                <span>Buscar</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="document_number-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="full_name">
                                        <span id="fullNameLabel">Nombre completo</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm" id="full_name" name="full_name"
                                        type="text" maxlength="255" required>
                                    <small class="form-text text-muted d-none" id="fullNameHelp">
                                        Nombre del representante o persona de contacto.
                                    </small>
                                    <div class="invalid-feedback" id="full_name-error"></div>
                                </div>
                                <div class="form-group col-md-6" id="businessNameGroup">
                                    <label class="small font-weight-bold text-secondary" for="business_name">
                                        Razón social
                                    </label>
                                    <input class="form-control form-control-sm" id="business_name"
                                        name="business_name" type="text" maxlength="255">
                                    <div class="invalid-feedback" id="business_name-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="phone">Teléfono</label>
                                    <input class="form-control form-control-sm" id="phone" name="phone"
                                        type="text" maxlength="30">
                                    <div class="invalid-feedback" id="phone-error"></div>
                                </div>
                                <div class="form-group col-md-5">
                                    <label class="small font-weight-bold text-secondary" for="email">Correo</label>
                                    <input class="form-control form-control-sm" id="email" name="email"
                                        type="email" maxlength="255">
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="form-group col-md-3">
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
                                <label class="small font-weight-bold text-secondary" for="address">Dirección</label>
                                <input class="form-control form-control-sm" id="address" name="address"
                                    type="text" maxlength="255">
                                <div class="invalid-feedback" id="address-error"></div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary"
                                    for="notes">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"
                                    placeholder="Información adicional relevante del propietario"></textarea>
                                <div class="invalid-feedback" id="notes-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveOwnerButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Propietario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
