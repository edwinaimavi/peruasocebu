<div class="modal fade" id="treatmentModal" tabindex="-1" role="dialog" aria-labelledby="treatmentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-pills text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="treatmentModalLabel">Nuevo Tratamiento</h5>
                        <small class="text-muted">Registro clinico, medicacion y seguimiento del ganado</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="treatmentForm" class="treatment-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf
                    <div id="treatment-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="treatment-section-title"><i class="fas fa-notes-medical"></i> Datos principales</div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">Ganado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="veterinarian_id">Veterinario</label>
                                    <select class="form-control form-control-sm" id="veterinarian_id" name="veterinarian_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($veterinarians as $veterinarian)
                                            <option value="{{ $veterinarian->id }}">{{ $veterinarian->full_name }}{{ $veterinarian->license_number ? ' - '.$veterinarian->license_number : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="veterinarian_id-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label class="small font-weight-bold text-secondary" for="treatment_date">Fecha del tratamiento <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="treatment_date" name="treatment_date" type="date" required>
                                    <div class="invalid-feedback" id="treatment_date-error"></div>
                                </div>
                                <div class="form-group col-md-7">
                                    <label class="small font-weight-bold text-secondary" for="treatment_name">Nombre del tratamiento <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="treatment_name" name="treatment_name" type="text" maxlength="255" required>
                                    <div class="invalid-feedback" id="treatment_name-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="treatment-section-title"><i class="fas fa-prescription-bottle-alt"></i> Medicacion</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="medicine">Medicamento</label>
                                    <input class="form-control form-control-sm" id="medicine" name="medicine" type="text" maxlength="255">
                                    <div class="invalid-feedback" id="medicine-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="dose">Dosis</label>
                                    <input class="form-control form-control-sm" id="dose" name="dose" type="text" maxlength="100">
                                    <div class="invalid-feedback" id="dose-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="duration">Duracion</label>
                                    <input class="form-control form-control-sm" id="duration" name="duration" type="text" maxlength="100">
                                    <div class="invalid-feedback" id="duration-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="treatment-section-title"><i class="fas fa-clipboard-list"></i> Motivo y observaciones</div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary" for="reason">Motivo</label>
                                <textarea class="form-control form-control-sm" id="reason" name="reason" rows="3"></textarea>
                                <div class="invalid-feedback" id="reason-error"></div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="observations">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="observations" name="observations" rows="3"></textarea>
                                <div class="invalid-feedback" id="observations-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveTreatmentButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Tratamiento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
