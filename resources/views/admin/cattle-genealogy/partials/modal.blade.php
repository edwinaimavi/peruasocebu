<div class="modal fade" id="genealogyModal" tabindex="-1" role="dialog" aria-labelledby="genealogyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-sitemap text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="genealogyModalLabel">Nuevo Registro Genealógico</h5>
                        <small class="text-muted">Relaciones familiares registradas o manuales</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="genealogyForm" class="genealogy-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="genealogy-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-paw"></i> Animal principal y relación
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">
                                        Animal principal <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">
                                                {{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="relation_type">
                                        Relación <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="relation_type" name="relation_type" required>
                                        <option value="">Seleccione</option>
                                        <option value="father">Padre</option>
                                        <option value="mother">Madre</option>
                                        <option value="paternal_grandfather">Abuelo paterno</option>
                                        <option value="paternal_grandmother">Abuela paterna</option>
                                        <option value="maternal_grandfather">Abuelo materno</option>
                                        <option value="maternal_grandmother">Abuela materna</option>
                                    </select>
                                    <div class="invalid-feedback" id="relation_type-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="generation_level">
                                        Generación <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="generation_level" name="generation_level" required>
                                        <option value="1">1 Padre / Madre</option>
                                        <option value="2">2 Abuelos</option>
                                        <option value="3">3 Bisabuelos</option>
                                    </select>
                                    <div class="invalid-feedback" id="generation_level-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-link"></i> Familiar registrado
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="relative_cattle_id">Familiar en el sistema</label>
                                <select class="form-control form-control-sm" id="relative_cattle_id" name="relative_cattle_id">
                                    <option value="">Familiar no registrado / ingreso manual</option>
                                    @foreach ($relativeCattle as $animal)
                                        <option value="{{ $animal->id }}">
                                            {{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }} ({{ $animal->sex === 'male' ? 'Macho' : 'Hembra' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Si seleccionas un familiar registrado, sus datos se completarán automáticamente.
                                </small>
                                <div class="invalid-feedback" id="relative_cattle_id-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-pen"></i> Datos manuales del familiar
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="relative_code">Código familiar</label>
                                    <input class="form-control form-control-sm" id="relative_code" name="relative_code"
                                        type="text" maxlength="120">
                                    <div class="invalid-feedback" id="relative_code-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="relative_name">Nombre familiar</label>
                                    <input class="form-control form-control-sm" id="relative_name" name="relative_name"
                                        type="text" maxlength="255">
                                    <div class="invalid-feedback" id="relative_name-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="breed_id">Raza familiar</label>
                                    <select class="form-control form-control-sm" id="breed_id" name="breed_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($breeds as $breed)
                                            <option value="{{ $breed->id }}">{{ $breed->code ? $breed->code.' - ' : '' }}{{ $breed->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="breed_id-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="purity_percentage">Pureza (%)</label>
                                    <input class="form-control form-control-sm" id="purity_percentage"
                                        name="purity_percentage" type="number" min="0" max="100" step="0.01">
                                    <div class="invalid-feedback" id="purity_percentage-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-clipboard-list"></i> Observaciones
                            </div>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"
                                placeholder="Observaciones genealógicas o fuente de información"></textarea>
                            <div class="invalid-feedback" id="notes-error"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveGenealogyButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Registro</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
