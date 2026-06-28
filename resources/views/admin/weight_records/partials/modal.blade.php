<div class="modal fade" id="weightRecordModal" tabindex="-1" role="dialog" aria-labelledby="weightRecordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-weight text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="weightRecordModalLabel">Nuevo Pesaje</h5>
                        <small class="text-muted">Control de peso y condicion corporal del ganado</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="weightRecordForm" class="weight-record-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf
                    <div id="weight-record-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="weight-section-title"><i class="fas fa-balance-scale"></i> Datos principales</div>
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
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="weight_kg">Peso en kg <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="weight_kg" name="weight_kg" type="number" min="0.01" max="9999.99" step="0.01" required>
                                    <div class="invalid-feedback" id="weight_kg-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="record_date">Fecha de pesaje <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="record_date" name="record_date" type="date" required>
                                    <div class="invalid-feedback" id="record_date-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="weight-section-title"><i class="fas fa-chart-line"></i> Evaluacion</div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label class="small font-weight-bold text-secondary" for="body_condition">Condicion corporal</label>
                                    <select class="form-control form-control-sm" id="body_condition" name="body_condition">
                                        <option value="">Sin dato</option>
                                        @foreach ($bodyConditions as $condition)
                                            <option value="{{ $condition }}">{{ $condition }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="body_condition-error"></div>
                                </div>
                                <div class="form-group col-md-7">
                                    <label class="small font-weight-bold text-secondary" for="observations">Observaciones</label>
                                    <textarea class="form-control form-control-sm" id="observations" name="observations" rows="4"></textarea>
                                    <div class="invalid-feedback" id="observations-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveWeightRecordButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Pesaje</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
