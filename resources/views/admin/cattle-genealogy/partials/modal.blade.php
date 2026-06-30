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
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ejemplo: si Titán será abuelo paterno de Rómulo, selecciona Rómulo como animal principal,
                        ubicación FF - Abuelo paterno y Titán como familiar.
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-paw"></i> Animal principal y relación
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">
                                        Animal hijo / animal principal <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">
                                                {{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Selecciona el animal al que le vas a registrar padre, madre o abuelos.
                                    </small>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="lineage_path">
                                        Ubicación dentro del linaje <span class="text-danger">*</span>
                                    </label>
                                    <input id="relation_type" name="relation_type" type="hidden" value="father">
                                    <select class="form-control form-control-sm" id="lineage_path" name="lineage_path" required>
                                        <option value="">Seleccione la ruta</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        F = padre, M = madre. Por ejemplo: FF significa padre del padre y MF significa padre de la madre.
                                    </small>
                                    <div class="genealogy-lineage-help mt-2" id="lineagePathHelp"></div>
                                    <div class="invalid-feedback" id="lineage_path-error"></div>
                                    <div class="invalid-feedback" id="relation_type-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="generation_level">
                                        Generacion <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="generation_level" name="generation_level" required>
                                        @foreach ($generationOptions as $level => $label)
                                            <option value="{{ $level }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="generation_level-error"></div>
                                </div>
                            </div>
                            <div class="genealogy-relation-preview d-none" id="lineageRelationPreview">
                                <div class="genealogy-relation-preview-title">
                                    <i class="fas fa-project-diagram"></i> Relación resultante
                                </div>
                                <div id="lineageRelationPreviewText"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="genealogy-section-title">
                                <i class="fas fa-link"></i> Familiar registrado
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="relative_cattle_id">Familiar que será asignado</label>
                                <select class="form-control form-control-sm" id="relative_cattle_id" name="relative_cattle_id">
                                    <option value="">Familiar no registrado / ingreso manual</option>
                                    @foreach ($relativeCattle as $animal)
                                        <option value="{{ $animal->id }}" data-sex="{{ $animal->sex }}">
                                            {{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }} ({{ $animal->sex === 'male' ? 'Macho' : 'Hembra' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted" id="relative_cattle_id-help">
                                    Si la relación es Madre, aquí debes seleccionar la hembra que será madre del animal principal.
                                    Si la relación es Padre, selecciona el macho que será padre.
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
