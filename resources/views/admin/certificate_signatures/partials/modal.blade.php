<div class="modal fade" id="certificateSignatureModal" tabindex="-1" role="dialog" aria-labelledby="certificateSignatureModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-signature text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="certificateSignatureModalLabel">Nueva Firma</h5>
                        <small class="text-muted">Firma y sello para certificados</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="certificateSignatureForm" class="certificate-signature-modal-form" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body p-3">
                    @csrf
                    <div id="certificate-signature-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="signature-section-title"><i class="fas fa-certificate"></i> Certificado</div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="certificate_id">Certificado <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" id="certificate_id" name="certificate_id" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($certificates as $certificate)
                                        <option value="{{ $certificate->id }}">
                                            {{ $certificate->certificate_number }} - {{ $certificate->cattle?->code ?: 'Sin ganado' }} - {{ $certificateTypes[$certificate->certificate_type] ?? ucfirst((string) $certificate->certificate_type) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="certificate_id-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="signature-section-title"><i class="fas fa-user-tie"></i> Datos de la persona</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="person_type">Tipo de persona <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="person_type" name="person_type" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($personTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="person_type-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="person_name">Nombre de la persona <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="person_name" name="person_name" type="text" maxlength="255" required>
                                    <div class="invalid-feedback" id="person_name-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="position">Cargo</label>
                                    <input class="form-control form-control-sm" id="position" name="position" type="text" maxlength="255">
                                    <div class="invalid-feedback" id="position-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="signature-section-title"><i class="fas fa-paperclip"></i> Archivos</div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="signature-upload-card">
                                        <div class="font-weight-bold text-success">Firma</div>
                                        <div class="text-muted small">Imagen JPG, PNG o WEBP - Max. 4 MB</div>
                                        <input class="d-none" id="signature_file" name="signature_file" type="file" accept=".jpg,.jpeg,.png,.webp">
                                        <div class="mt-2">
                                            <label for="signature_file" class="btn btn-photo-upload mb-0">
                                                <i class="fas fa-upload mr-1"></i> Seleccionar firma
                                            </label>
                                        </div>
                                        <div class="signature-preview" id="signaturePreview">
                                            <span class="text-muted small">Sin firma registrada</span>
                                        </div>
                                        <div class="text-muted small mt-2" id="signatureFileName">Ningun archivo seleccionado</div>
                                        <div class="invalid-feedback d-block" id="signature_file-error"></div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="signature-upload-card">
                                        <div class="font-weight-bold text-success">Sello</div>
                                        <div class="text-muted small">Imagen JPG, PNG o WEBP - Max. 4 MB</div>
                                        <input class="d-none" id="seal_file" name="seal_file" type="file" accept=".jpg,.jpeg,.png,.webp">
                                        <div class="mt-2">
                                            <label for="seal_file" class="btn btn-photo-upload mb-0">
                                                <i class="fas fa-upload mr-1"></i> Seleccionar sello
                                            </label>
                                        </div>
                                        <div class="signature-preview" id="sealPreview">
                                            <span class="text-muted small">Sin sello registrado</span>
                                        </div>
                                        <div class="text-muted small mt-2" id="sealFileName">Ningun archivo seleccionado</div>
                                        <div class="invalid-feedback d-block" id="seal_file-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveCertificateSignatureButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Firma</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
