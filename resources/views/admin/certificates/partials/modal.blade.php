<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="certificateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-certificate text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="certificateModalLabel">Nuevo Certificado</h5>
                        <small class="text-muted">Emision documental del ganado</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="certificateForm" class="certificate-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf
                    <div id="certificate-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3 d-none" id="certificateReadonlyCard">
                        <div class="card-body">
                            <div class="certificate-section-title"><i class="fas fa-fingerprint"></i> Identificacion</div>
                            <div class="form-row">
                                <div class="form-group col-md-4 mb-md-0">
                                    <label class="small font-weight-bold text-secondary">Nro. certificado</label>
                                    <input class="form-control form-control-sm" id="readonly_certificate_number" type="text" readonly>
                                </div>
                                <div class="form-group col-md-4 mb-md-0">
                                    <label class="small font-weight-bold text-secondary">Codigo verificacion</label>
                                    <input class="form-control form-control-sm" id="readonly_verification_code" type="text" readonly>
                                </div>
                                <div class="form-group col-md-4 mb-0">
                                    <label class="small font-weight-bold text-secondary">PDF generado</label>
                                    <div><a class="btn btn-outline-primary btn-sm d-none" id="readonly_pdf_link" href="#" target="_blank" rel="noopener"><i class="fas fa-file-pdf mr-1"></i> Ver PDF</a></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="certificate-section-title"><i class="fas fa-award"></i> Datos del certificado</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">Ganado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="certificate_type">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="certificate_type" name="certificate_type" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($certificateTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="certificate_type-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="issue_date">Fecha emision <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="issue_date" name="issue_date" type="date" required>
                                    <div class="invalid-feedback" id="issue_date-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="purity_percentage">Pureza</label>
                                    <input class="form-control form-control-sm" id="purity_percentage" name="purity_percentage" type="number" min="0" max="100" step="0.01">
                                    <div class="invalid-feedback" id="purity_percentage-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-3 mb-md-0">
                                    <label class="small font-weight-bold text-secondary" for="status">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="status" name="status" required>
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="certificate-section-title"><i class="fas fa-link"></i> Entidades relacionadas</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="ranch_id">Criadero emisor</label>
                                    <select class="form-control form-control-sm" id="ranch_id" name="ranch_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($ranches as $ranch)
                                            <option value="{{ $ranch->id }}">{{ $ranch->business_name ?: $ranch->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="ranch_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="owner_id">Propietario actual</label>
                                    <select class="form-control form-control-sm" id="owner_id" name="owner_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="owner_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="veterinarian_id">Veterinario / certificador</label>
                                    <select class="form-control form-control-sm" id="veterinarian_id" name="veterinarian_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($veterinarians as $veterinarian)
                                            <option value="{{ $veterinarian->id }}">{{ $veterinarian->full_name }}{{ $veterinarian->license_number ? ' - '.$veterinarian->license_number : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="veterinarian_id-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="certificate-section-title"><i class="fas fa-clipboard-list"></i> Observaciones</div>
                            <textarea class="form-control form-control-sm" id="observations" name="observations" rows="4"></textarea>
                            <div class="invalid-feedback" id="observations-error"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveCertificateButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Certificado</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
