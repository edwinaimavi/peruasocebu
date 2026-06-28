<div class="modal fade" id="reproductionRecordModal" tabindex="-1" role="dialog" aria-labelledby="reproductionRecordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-venus-mars text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="reproductionRecordModalLabel">Nuevo Registro</h5>
                        <small class="text-muted">Cruce, control de prenez, parto y cria vinculada</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="reproductionRecordForm" class="reproduction-record-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf
                    <div id="reproduction-record-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="reproduction-section-title"><i class="fas fa-paw"></i> Animales</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">Animal principal <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($femaleCattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }} - Hembra</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="partner_cattle_id">Pareja / reproductor</label>
                                    <select class="form-control form-control-sm" id="partner_cattle_id" name="partner_cattle_id">
                                        <option value="">Sin pareja registrada</option>
                                        @foreach ($maleCattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }} - Macho</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="partner_cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="offspring_cattle_id">Cria nacida</label>
                                    <select class="form-control form-control-sm" id="offspring_cattle_id" name="offspring_cattle_id">
                                        <option value="">Sin cria vinculada</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">La cria nacida es opcional. Solo seleccionela si ya fue registrada como ganado.</small>
                                    <div class="invalid-feedback" id="offspring_cattle_id-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="reproduction-section-title"><i class="fas fa-heartbeat"></i> Datos reproductivos</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="method">Metodo <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="method" name="method" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($methods as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="method-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="reproduction_date">Fecha reproductiva <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="reproduction_date" name="reproduction_date" type="date" required>
                                    <div class="invalid-feedback" id="reproduction_date-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="pregnancy_check_date">Fecha control de prenez</label>
                                    <input class="form-control form-control-sm" id="pregnancy_check_date" name="pregnancy_check_date" type="date">
                                    <div class="invalid-feedback" id="pregnancy_check_date-error"></div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="pregnancy_result">Resultado de prenez <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="pregnancy_result" name="pregnancy_result" required>
                                        @foreach ($pregnancyResults as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="pregnancy_result-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="birth_date">Fecha de parto</label>
                                    <input class="form-control form-control-sm" id="birth_date" name="birth_date" type="date">
                                    <div class="invalid-feedback" id="birth_date-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="reproduction-section-title"><i class="fas fa-clipboard-list"></i> Observaciones</div>
                            <textarea class="form-control form-control-sm" id="observations" name="observations" rows="4"></textarea>
                            <div class="invalid-feedback" id="observations-error"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveReproductionRecordButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Registro</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
