<div class="modal fade" id="ranchModal" tabindex="-1" role="dialog" aria-labelledby="ranchModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-warehouse text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ranchModalLabel">Nuevo Criadero / Hacienda</h5>
                        <small class="text-muted">Información institucional · campos obligatorios (*)</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="ranchForm" class="ranch-modal-form" enctype="multipart/form-data" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="ranch-error-messages" class="alert alert-danger d-none"></div>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 rounded-lg shadow-sm h-100">
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-7">
                                            <label class="small font-weight-bold text-secondary" for="name">
                                                Nombre del criadero / hacienda <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control form-control-sm" id="name" name="name"
                                                type="text" maxlength="255" required>
                                            <div class="invalid-feedback" id="name-error"></div>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label class="small font-weight-bold text-secondary" for="business_name">
                                                Razón social
                                            </label>
                                            <input class="form-control form-control-sm" id="business_name"
                                                name="business_name" type="text" maxlength="255">
                                            <div class="invalid-feedback" id="business_name-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label class="small font-weight-bold text-secondary" for="document_type">
                                                Tipo de documento
                                            </label>
                                            <select class="form-control form-control-sm" id="document_type"
                                                name="document_type">
                                                <option value="">Seleccione</option>
                                                <option value="RUC">RUC</option>
                                                <option value="DNI">DNI</option>
                                                <option value="CE">CE</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                            <div class="invalid-feedback" id="document_type-error"></div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary" for="document_number">
                                                Número de documento
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input class="form-control" id="document_number"
                                                    name="document_number" type="text" maxlength="30"
                                                    inputmode="numeric">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" id="btnSearchDocument"
                                                        type="button" title="Consultar DNI o RUC">
                                                        <i class="fas fa-search mr-1"></i>
                                                        <span>Buscar</span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" id="document_number-error"></div>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label class="small font-weight-bold text-secondary" for="representative_name">
                                                Responsable / representante
                                            </label>
                                            <input class="form-control form-control-sm" id="representative_name"
                                                name="representative_name" type="text" maxlength="255">
                                            <div class="invalid-feedback" id="representative_name-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label class="small font-weight-bold text-secondary" for="address">
                                                Dirección
                                            </label>
                                            <input class="form-control form-control-sm" id="address" name="address"
                                                type="text" maxlength="255">
                                            <div class="invalid-feedback" id="address-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary"
                                                for="department">Departamento</label>
                                            <input class="form-control form-control-sm" id="department" name="department"
                                                type="text" maxlength="100">
                                            <div class="invalid-feedback" id="department-error"></div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary"
                                                for="province">Provincia</label>
                                            <input class="form-control form-control-sm" id="province" name="province"
                                                type="text" maxlength="100">
                                            <div class="invalid-feedback" id="province-error"></div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary"
                                                for="district">Distrito</label>
                                            <input class="form-control form-control-sm" id="district" name="district"
                                                type="text" maxlength="100">
                                            <div class="invalid-feedback" id="district-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary"
                                                for="phone">Teléfono</label>
                                            <input class="form-control form-control-sm" id="phone" name="phone"
                                                type="text" maxlength="30">
                                            <div class="invalid-feedback" id="phone-error"></div>
                                        </div>
                                        <div class="form-group col-md-5">
                                            <label class="small font-weight-bold text-secondary"
                                                for="email">Correo</label>
                                            <input class="form-control form-control-sm" id="email" name="email"
                                                type="email" maxlength="255">
                                            <div class="invalid-feedback" id="email-error"></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="small font-weight-bold text-secondary" for="status">
                                                Estado <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control form-control-sm" id="status" name="status"
                                                required>
                                                <option value="active">Activo</option>
                                                <option value="inactive">Inactivo</option>
                                            </select>
                                            <div class="invalid-feedback" id="status-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold text-secondary"
                                            for="description">Descripción</label>
                                        <textarea class="form-control form-control-sm" id="description" name="description" rows="4"
                                            placeholder="Descripción, especialidad o información relevante del criadero"></textarea>
                                        <div class="invalid-feedback" id="description-error"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 mt-3 mt-lg-0">
                            <div class="card border-0 rounded-lg shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-secondary mb-3">
                                        <i class="fas fa-images mr-1"></i> Identidad visual
                                    </h6>

                                    @foreach ([
                                        ['input' => 'logo', 'label' => 'Logo', 'icon' => 'fa-image'],
                                        ['input' => 'seal', 'label' => 'Sello', 'icon' => 'fa-certificate'],
                                        ['input' => 'signature', 'label' => 'Firma', 'icon' => 'fa-signature'],
                                    ] as $file)
                                        <div class="ranch-file-card mb-3">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="small font-weight-bold text-secondary">
                                                    <i class="fas {{ $file['icon'] }} mr-1"></i> {{ $file['label'] }}
                                                </span>
                                                <label class="btn btn-sm btn-light border mb-0"
                                                    for="{{ $file['input'] }}">
                                                    <i class="fas fa-upload mr-1"></i> Seleccionar
                                                </label>
                                            </div>
                                            <img class="ranch-file-preview {{ $file['input'] === 'logo' ? 'ranch-file-preview--logo' : 'ranch-file-preview--compact' }} d-none"
                                                id="{{ $file['input'] }}Preview"
                                                alt="Vista previa de {{ strtolower($file['label']) }}">
                                            <div class="ranch-file-placeholder text-center text-muted py-3"
                                                id="{{ $file['input'] }}Placeholder">
                                                <i class="fas {{ $file['icon'] }} fa-2x mb-2"></i>
                                                <div class="small">Sin archivo</div>
                                            </div>
                                            <input class="d-none ranch-file-input" id="{{ $file['input'] }}"
                                                name="{{ $file['input'] }}" type="file"
                                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                                data-preview="{{ $file['input'] }}Preview"
                                                data-placeholder="{{ $file['input'] }}Placeholder">
                                            <div class="invalid-feedback d-block"
                                                id="{{ $file['input'] }}-error"></div>
                                        </div>
                                    @endforeach

                                    <small class="text-muted">Formatos JPG, PNG o WEBP. Máximo 4 MB por archivo.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveRanchButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Criadero</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
