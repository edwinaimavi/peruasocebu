<div class="modal fade" id="veterinaryRecordModal" tabindex="-1" role="dialog"
    aria-labelledby="veterinaryRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-stethoscope text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="veterinaryRecordModalLabel">Nueva Revision</h5>
                        <small class="text-muted">Atencion veterinaria y control sanitario</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="veterinaryRecordForm" class="veterinary-record-modal-form" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body p-3">
                    @csrf
                    <div id="veterinary-record-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="veterinary-section-title"><i class="fas fa-notes-medical"></i> Datos principales</div>
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
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="veterinarian_id">Veterinario</label>
                                    <select class="form-control form-control-sm" id="veterinarian_id" name="veterinarian_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($veterinarians as $veterinarian)
                                            <option value="{{ $veterinarian->id }}">
                                                {{ $veterinarian->full_name }}{{ $veterinarian->license_number ? ' - '.$veterinarian->license_number : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="veterinarian_id-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="record_date">Fecha <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="record_date" name="record_date" type="date" required>
                                    <div class="invalid-feedback" id="record_date-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="next_visit_date">Proxima visita</label>
                                    <input class="form-control form-control-sm" id="next_visit_date" name="next_visit_date" type="date">
                                    <div class="invalid-feedback" id="next_visit_date-error"></div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="record_type">Tipo de revision <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" id="record_type" name="record_type" required>
                                    <option value="">Seleccione</option>
                                    <option value="checkup">Revision</option>
                                    <option value="illness">Enfermedad</option>
                                    <option value="control">Control</option>
                                    <option value="certification">Certificacion</option>
                                    <option value="emergency">Emergencia</option>
                                    <option value="other">Otro</option>
                                </select>
                                <div class="invalid-feedback" id="record_type-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="veterinary-section-title"><i class="fas fa-clipboard-list"></i> Diagnostico y tratamiento</div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary" for="diagnosis">Diagnostico</label>
                                <textarea class="form-control form-control-sm" id="diagnosis" name="diagnosis" rows="3"></textarea>
                                <div class="invalid-feedback" id="diagnosis-error"></div>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary" for="treatment">Tratamiento indicado</label>
                                <textarea class="form-control form-control-sm" id="treatment" name="treatment" rows="3"></textarea>
                                <div class="invalid-feedback" id="treatment-error"></div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="observations">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="observations" name="observations" rows="3"></textarea>
                                <div class="invalid-feedback" id="observations-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="veterinary-section-title"><i class="fas fa-paperclip"></i> Documento adjunto</div>
                            <div class="veterinary-file-card">
                                <div class="veterinary-file-icon"><i class="fas fa-file-medical"></i></div>
                                <div class="flex-fill">
                                    <div class="font-weight-bold text-success">Archivo adjunto</div>
                                    <div class="text-muted small">PDF, imagen o Word - Max. 5 MB</div>
                                    <input class="d-none" id="document_file" name="document_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                                    <div class="mt-2">
                                        <label for="document_file" class="btn btn-photo-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar documento
                                        </label>
                                        <a class="btn btn-outline-primary d-none ml-2" id="currentDocumentLink" href="#" target="_blank" rel="noopener">
                                            <i class="fas fa-eye mr-1"></i> Ver documento
                                        </a>
                                    </div>
                                    <div class="text-muted small mt-2" id="documentFileName">Ningun archivo seleccionado</div>
                                    <div class="invalid-feedback d-block" id="document_file-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveVeterinaryRecordButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Revision</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
