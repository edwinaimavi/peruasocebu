<div class="modal fade" id="cattleSaleModal" tabindex="-1" role="dialog" aria-labelledby="cattleSaleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-handshake text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="cattleSaleModalLabel">Nueva Venta</h5>
                        <small class="text-muted">Operacion comercial y cambio de propietario</small>
                    </div>
                </div>
                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="cattleSaleForm" class="cattle-sale-modal-form" autocomplete="off" enctype="multipart/form-data">
                <div class="modal-body p-3">
                    @csrf
                    <div id="cattle-sale-error-messages" class="alert alert-danger d-none"></div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="sale-section-title"><i class="fas fa-paw"></i> Datos del ganado</div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="cattle_id">Ganado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="cattle_id" name="cattle_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($cattle as $animal)
                                            <option value="{{ $animal->id }}">{{ $animal->code }} - {{ $animal->name ?: 'Sin nombre' }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted d-none" id="sellerHelp">Este ganado no tiene propietario actual registrado.</small>
                                    <div class="invalid-feedback" id="cattle_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="seller_owner_id">Vendedor</label>
                                    <select class="form-control form-control-sm" id="seller_owner_id" name="seller_owner_id">
                                        <option value="">Seleccione</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="seller_owner_id-error"></div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary" for="buyer_owner_id">Comprador <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="buyer_owner_id" name="buyer_owner_id" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->owner_type === 'company' && $owner->business_name ? $owner->business_name : $owner->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="buyer_owner_id-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="sale-section-title"><i class="fas fa-file-invoice-dollar"></i> Datos de venta</div>
                            <div class="form-row">
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="sale_date">Fecha de venta <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="sale_date" name="sale_date" type="date" required>
                                    <div class="invalid-feedback" id="sale_date-error"></div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="small font-weight-bold text-secondary" for="sale_price">Precio <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" id="sale_price" name="sale_price" type="number" min="0" step="0.01" required>
                                    <div class="invalid-feedback" id="sale_price-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="currency">Moneda</label>
                                    <select class="form-control form-control-sm" id="currency" name="currency" required>
                                        <option value="PEN">PEN</option>
                                        <option value="USD">USD</option>
                                    </select>
                                    <div class="invalid-feedback" id="currency-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="payment_method">Pago <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="payment_method" name="payment_method" required>
                                        <option value="">Seleccione</option>
                                        <option value="cash">Efectivo</option>
                                        <option value="transfer">Transferencia</option>
                                        <option value="yape">Yape</option>
                                        <option value="plin">Plin</option>
                                        <option value="deposit">Deposito</option>
                                        <option value="other">Otro</option>
                                    </select>
                                    <div class="invalid-feedback" id="payment_method-error"></div>
                                </div>
                                <div class="form-group col-md-2">
                                    <label class="small font-weight-bold text-secondary" for="status">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="status" name="status" required>
                                        <option value="pending">Pendiente</option>
                                        <option value="completed">Completado</option>
                                        <option value="cancelled">Anulado</option>
                                    </select>
                                    <div class="invalid-feedback" id="status-error"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-body">
                            <div class="sale-section-title"><i class="fas fa-paperclip"></i> Documento</div>
                            <div class="sale-file-card mb-3">
                                <div class="sale-file-icon"><i class="fas fa-file-contract"></i></div>
                                <div class="flex-fill">
                                    <div class="font-weight-bold text-success">Contrato o documento</div>
                                    <div class="text-muted small">PDF, imagen o Word - Max. 5 MB</div>
                                    <input class="d-none" id="contract_file" name="contract_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx">
                                    <div class="mt-2">
                                        <label for="contract_file" class="btn btn-photo-upload mb-0">
                                            <i class="fas fa-upload mr-1"></i> Seleccionar contrato
                                        </label>
                                        <a class="btn btn-outline-primary d-none ml-2" id="currentContractLink" href="#" target="_blank" rel="noopener">
                                            <i class="fas fa-eye mr-1"></i> Ver contrato
                                        </a>
                                    </div>
                                    <div class="text-muted small mt-2" id="contractFileName">Ningun archivo seleccionado</div>
                                    <div class="invalid-feedback d-block" id="contract_file-error"></div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary" for="notes">Observaciones</label>
                                <textarea class="form-control form-control-sm" id="notes" name="notes" rows="4"></textarea>
                                <div class="invalid-feedback" id="notes-error"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-end">
                    <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cerrar
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveCattleSaleButton">
                        <i class="fas fa-save mr-1"></i>
                        <span>Guardar Venta</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
