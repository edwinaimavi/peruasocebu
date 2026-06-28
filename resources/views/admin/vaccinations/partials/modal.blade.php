<div class="modal fade" id="vaccinationModal" tabindex="-1" role="dialog" aria-labelledby="vaccinationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-syringe text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="vaccinationModalLabel">Nueva Vacuna</h5>
                        <small class="text-muted">Control de inmunizacion y proximas aplicaciones</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="vaccinationForm" class="vaccination-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf
                    <div id="vaccination-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="vaccination-section-title"><i class="fas fa-shield-alt"></i> Datos principales</div>
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
                                            <option value="{{ $veterinarian->id }}">{{ $veterinarian->full_name }}{{ $veterinarian->license_number ? ' - '.$veterinarian->license_number : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="veterinarian_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="vaccine_name">Nombre de la vacuna <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="vaccine_name" name="vaccine_name" type="text" maxlength="255" required>
                                    <div class="invalid-feedback" id="vaccine_name-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="dose">Dosis</label>
                                    <input class="form-control form-control-sm" id="dose" name="dose" type="text" maxlength="100">
                                    <div class="invalid-feedback" id="dose-error"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="batch_number">Numero de lote</label>
                                    <input class="form-control form-control-sm" id="batch_number" name="batch_number" type="text" maxlength="100">
                                    <div class="invalid-feedback" id="batch_number-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="vaccination-section-title"><i class="fas fa-calendar-alt"></i> Fechas</div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="application_date">Fecha aplicada <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="application_date" name="application_date" type="date" required>
                                    <div class="invalid-feedback" id="application_date-error"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="next_due_date">Proxima dosis</label>
                                    <input class="form-control form-control-sm" id="next_due_date" name="next_due_date" type="date">
                                    <div class="invalid-feedback" id="next_due_date-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="vaccination-section-title"><i class="fas fa-clipboard-list"></i> Observaciones</div>
                            <textarea class="form-control form-control-sm" id="observations" name="observations" rows="4"></textarea>
                            <div class="invalid-feedback" id="observations-error"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveVaccinationButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Vacuna</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
