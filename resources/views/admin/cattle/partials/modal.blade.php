<div class="modal fade" id="cattleModal" tabindex="-1" role="dialog" aria-labelledby="cattleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-paw text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="cattleModalLabel">Nuevo Ganado</h5>
                        <small class="text-muted">Registro productivo, sanitario y genealógico básico</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="cattleForm" class="cattle-modal-form" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body p-3">
                    @csrf

                    <div id="cattle-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="cattle-section-title">
                                <i class="fas fa-fingerprint"></i> Identificación
                            </div>

                            <div class="cattle-photo-card mb-3">
                                <div class="cattle-photo-preview-wrap">
                                    <img id="mainPhotoPreview" class="cattle-photo-preview d-none" src=""
                                        alt="Foto principal del ganado">
                                    <div id="mainPhotoPlaceholder" class="cattle-photo-placeholder">
                                        <i class="fas fa-paw"></i>
                                        <span>Sin foto</span>
                                    </div>
                                </div>
                                <div class="cattle-photo-controls">
                                    <div class="cattle-photo-title">
                                        <i class="fas fa-camera mr-1"></i>
                                        Foto principal del ganado
                                    </div>
                                    <div class="cattle-photo-subtitle">JPG, PNG o WEBP · Máx. 4 MB</div>
                                    <input class="d-none" id="main_photo" name="main_photo" type="file"
                                        accept="image/jpeg,image/png,image/webp">
                                    <div class="cattle-photo-actions">
                                        <label for="main_photo" class="btn btn-photo-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar foto
                                        </label>
                                        <button class="btn btn-photo-remove d-none" id="btnRemoveCattlePhotoPreview"
                                            type="button">
                                            <i class="fas fa-times mr-1"></i> Quitar
                                        </button>
                                    </div>
                                    <div class="cattle-photo-filename" id="mainPhotoFileName">Ningún archivo seleccionado</div>
                                    <div class="invalid-feedback d-block" id="main_photo-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="code">
                                        Código <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm text-uppercase" id="code" name="code"
                                        type="text" maxlength="50" placeholder="Se generará automáticamente" readonly>
                                    <small class="form-text text-muted">Se genera según la raza seleccionada.</small>
                                    <div class="invalid-feedback" id="code-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="name">
                                        Nombre del animal <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm" id="name" name="name" type="text"
                                        maxlength="255" required>
                                    <div class="invalid-feedback" id="name-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="breed_id">
                                        Raza <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="breed_id" name="breed_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($breeds as $breed)
                                            <option value="{{ $breed->id }}" data-code="{{ $breed->code }}"
                                                data-name="{{ $breed->name }}">
                                                {{ $breed->code ? $breed->code.' - ' : '' }}{{ $breed->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="breed_id-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="sex">
                                        Sexo <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="sex" name="sex" required>
                                        <option value="">Seleccione</option>
                                        <option value="male">Macho</option>
                                        <option value="female">Hembra</option>
                                    </select>
                                    <div class="invalid-feedback" id="sex-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="birth_date">Fecha de nacimiento</label>
                                    <input class="form-control form-control-sm" id="birth_date" name="birth_date" type="date">
                                    <div class="invalid-feedback" id="birth_date-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="color">Color</label>
                                    <input class="form-control form-control-sm" id="color" name="color" type="text"
                                        maxlength="120">
                                    <div class="invalid-feedback" id="color-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="purity_percentage">Pureza racial (%)</label>
                                    <input class="form-control form-control-sm" id="purity_percentage"
                                        name="purity_percentage" type="number" min="0" max="100" step="0.01">
                                    <div class="invalid-feedback" id="purity_percentage-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="cattle-section-title">
                                <i class="fas fa-map-marker-alt"></i> Ubicación y propietario
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="ranch_id">
                                        Criadero / Hacienda <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="ranch_id" name="ranch_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($ranches as $ranch)
                                            <option value="{{ $ranch->id }}">{{ $ranch->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="ranch_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="current_owner_id">Propietario actual</label>
                                    <select class="form-control form-control-sm" id="current_owner_id" name="current_owner_id">
                                        <option value="">Sin propietario asignado</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">
                                                {{ $owner->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="current_owner_id-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="status">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="status" name="status" required>
                                        <option value="active">Activo</option>
                                        <option value="dead">Fallecido</option>
                                        <option value="discarded">Descartado</option>
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="sale_status">
                                        Venta <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="sale_status" name="sale_status" required>
                                        <option value="not_available">No disponible</option>
                                        <option value="available">Disponible</option>
                                        <option value="reserved">Reservado</option>
                                        <option value="sold">Vendido</option>
                                    </select>
                                    <div class="invalid-feedback" id="sale_status-error"></div>
                                </div>
                            </div>

                            <div class="custom-control custom-switch">
                                <input class="custom-control-input" id="is_public" name="is_public" type="checkbox" value="1" checked>
                                <label class="custom-control-label small font-weight-bold text-secondary" for="is_public">
                                    Visible en consulta pública
                                </label>
                                <div class="invalid-feedback d-block" id="is_public-error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="cattle-section-title">
                                <i class="fas fa-ruler-combined"></i> Datos físicos
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="weight_kg">Peso kg</label>
                                    <input class="form-control form-control-sm" id="weight_kg" name="weight_kg"
                                        type="number" min="0" step="0.01">
                                    <div class="invalid-feedback" id="weight_kg-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="height_cm">Altura cm</label>
                                    <input class="form-control form-control-sm" id="height_cm" name="height_cm"
                                        type="number" min="0" step="0.01">
                                    <div class="invalid-feedback" id="height_cm-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="ear_tag">Arete / placa</label>
                                    <input class="form-control form-control-sm" id="ear_tag" name="ear_tag"
                                        type="text" maxlength="120">
                                    <div class="invalid-feedback" id="ear_tag-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="chip_number">Número de chip</label>
                                    <input class="form-control form-control-sm" id="chip_number" name="chip_number"
                                        type="text" maxlength="120">
                                    <div class="invalid-feedback" id="chip_number-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="cattle-section-title">
                                <i class="fas fa-dna"></i> Genealogía básica
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="father_id">Padre</label>
                                    <select class="form-control form-control-sm" id="father_id" name="father_id">
                                        <option value="">No registrado</option>
                                        @foreach ($fathers as $father)
                                            <option value="{{ $father->id }}">
                                                {{ $father->code }} - {{ $father->name ?: 'Sin nombre' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="father_id-error"></div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="mother_id">Madre</label>
                                    <select class="form-control form-control-sm" id="mother_id" name="mother_id">
                                        <option value="">No registrado</option>
                                        @foreach ($mothers as $mother)
                                            <option value="{{ $mother->id }}">
                                                {{ $mother->code }} - {{ $mother->name ?: 'Sin nombre' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="mother_id-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="cattle-section-title">
                                <i class="fas fa-clipboard-list"></i> Observaciones
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="observations">Observaciones generales</label>
                                <textarea class="form-control form-control-sm" id="observations" name="observations" rows="4"
                                    placeholder="Información adicional relevante del animal"></textarea>
                                <div class="invalid-feedback" id="observations-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveCattleButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Ganado</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
