<div class="modal fade" id="veterinarianModal" tabindex="-1" role="dialog" aria-labelledby="veterinarianModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-user-md text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="veterinarianModalLabel">Nuevo Veterinario</h5>
                        <small class="text-muted">Información profesional · campos obligatorios (*)</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="veterinarianForm" class="veterinarian-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="veterinarian-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="form-row">
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
                                <div class="form-group col-md-4">
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

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="full_name">
                                        Nombre completo <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm" id="full_name" name="full_name"
                                        type="text" maxlength="255" required>
                                    <div class="invalid-feedback" id="full_name-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="license_number">
                                        Colegiatura
                                    </label>
                                    <input class="form-control form-control-sm" id="license_number"
                                        name="license_number" type="text" maxlength="100">
                                    <div class="invalid-feedback" id="license_number-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="specialty">
                                        Especialidad
                                    </label>
                                    <input class="form-control form-control-sm" id="specialty" name="specialty"
                                        type="text" maxlength="150">
                                    <div class="invalid-feedback" id="specialty-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="phone">Teléfono</label>
                                    <input class="form-control form-control-sm" id="phone" name="phone"
                                        type="text" maxlength="30">
                                    <div class="invalid-feedback" id="phone-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="email">Correo</label>
                                    <input class="form-control form-control-sm" id="email" name="email"
                                        type="email" maxlength="255">
                                    <div class="invalid-feedback" id="email-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="address">Dirección</label>
                                    <input class="form-control form-control-sm" id="address" name="address"
                                        type="text" maxlength="255">
                                    <div class="invalid-feedback" id="address-error"></div>
                                </div>
                            </div>

                            <div class="veterinarian-signature-card mb-3">
                                <div class="veterinarian-signature-preview-wrap">
                                    <img id="signaturePreview" class="veterinarian-signature-preview d-none"
                                        src="" alt="Firma digital">
                                    <div id="signaturePlaceholder" class="veterinarian-signature-placeholder">
                                        <i class="fas fa-signature"></i>
                                        <span>Sin firma</span>
                                    </div>
                                </div>
                                <div class="veterinarian-signature-controls">
                                    <div class="veterinarian-signature-title">
                                        <i class="fas fa-pen-nib mr-1"></i>
                                        Firma digital
                                    </div>
                                    <div class="veterinarian-signature-subtitle">JPG, PNG o WEBP · Máx. 4 MB</div>
                                    <input class="d-none" id="signature" name="signature" type="file"
                                        accept="image/jpeg,image/png,image/webp">
                                    <div class="veterinarian-signature-actions">
                                        <label for="signature" class="btn btn-signature-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar firma
                                        </label>
                                        <button class="btn btn-signature-remove d-none" id="btnRemoveSignaturePreview"
                                            type="button">
                                            <i class="fas fa-times mr-1"></i> Quitar
                                        </button>
                                    </div>
                                    <div class="veterinarian-signature-filename" id="signatureFileName">
                                        Ningún archivo seleccionado
                                    </div>
                                    <div class="invalid-feedback d-block" id="signature-error"></div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary"
                                    for="notes">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"
                                    placeholder="Información adicional relevante del veterinario"></textarea>
                                <div class="invalid-feedback" id="notes-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveVeterinarianButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Veterinario</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
