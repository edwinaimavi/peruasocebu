<div class="modal fade" id="ownershipHistoryModal" tabindex="-1" role="dialog"
    aria-labelledby="ownershipHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-user-clock text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ownershipHistoryModalLabel">Nuevo Historial</h5>
                        <small class="text-muted">Cambios de propietario y periodo de posesion</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="ownershipHistoryForm" class="ownership-history-modal-form" autocomplete="off">
                <div class="modal-body p-3">
                    @csrf

                    <div id="ownership-history-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="ownership-section-title">
                                <i class="fas fa-exchange-alt"></i> Datos principales
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">
                                        Ganado <span class="text-danger">*</span>
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
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="owner_id">
                                        Propietario <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="owner_id" name="owner_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">
                                                {{ $owner->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="owner_id-error"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="start_date">
                                        Fecha desde <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control form-control-sm" id="start_date" name="start_date"
                                        type="date" required>
                                    <div class="invalid-feedback" id="start_date-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="end_date">Fecha hasta</label>
                                    <input class="form-control form-control-sm" id="end_date" name="end_date" type="date">
                                    <div class="invalid-feedback" id="end_date-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="acquisition_type">
                                        Tipo de adquisicion <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control form-control-sm" id="acquisition_type"
                                        name="acquisition_type" required>
                                        <option value="">Seleccione</option>
                                        <option value="birth">Nacimiento</option>
                                        <option value="purchase">Compra</option>
                                        <option value="sale">Venta</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="donation">Donacion</option>
                                        <option value="other">Otro</option>
                                    </select>
                                    <div class="invalid-feedback" id="acquisition_type-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="ownership-section-title">
                                <i class="fas fa-file-invoice-dollar"></i> Documento y valores
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="small font-weight-bold text-secondary" for="document_reference">
                                        Referencia de documento
                                    </label>
                                    <input class="form-control form-control-sm" id="document_reference"
                                        name="document_reference" type="text" maxlength="255">
                                    <div class="invalid-feedback" id="document_reference-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="price">Precio</label>
                                    <input class="form-control form-control-sm" id="price" name="price" type="number"
                                        min="0" step="0.01">
                                    <div class="invalid-feedback" id="price-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="currency">Moneda</label>
                                    <select class="form-control form-control-sm" id="currency" name="currency">
                                        <option value="PEN">PEN</option>
                                        <option value="USD">USD</option>
                                    </select>
                                    <div class="invalid-feedback" id="currency-error"></div>
                                </div>
                            </div>

                            <div class="custom-control custom-switch mb-3">
                                <input class="custom-control-input" id="is_current" name="is_current" type="checkbox"
                                    value="1">
                                <label class="custom-control-label small font-weight-bold text-secondary"
                                    for="is_current">
                                    Es propietario actual
                                </label>
                                <div class="invalid-feedback d-block" id="is_current-error"></div>
                            </div>

                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="notes">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"
                                    placeholder="Informacion adicional del cambio de propietario"></textarea>
                                <div class="invalid-feedback" id="notes-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveOwnershipHistoryButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Historial</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
