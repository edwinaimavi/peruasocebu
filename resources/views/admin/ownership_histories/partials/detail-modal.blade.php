<div class="modal fade" id="ownershipHistoryDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="ownershipHistoryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-history text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ownershipHistoryDetailModalLabel">Detalle del Historial</h5>
                        <small class="text-muted" id="detailOwnershipSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="ownership-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="ownership-detail-photo-wrap mx-auto">
                                <img id="detailOwnershipCattlePhoto" class="ownership-detail-photo d-none" src=""
                                    alt="Foto del ganado">
                                <div id="detailOwnershipCattlePhotoPlaceholder" class="ownership-detail-photo-placeholder">
                                    <i class="fas fa-paw"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="ownership-detail-label">Ganado</div>
                            <div class="ownership-detail-value h4 mb-0" id="detailOwnershipCattle">-</div>
                            <div class="text-muted mt-1" id="detailOwnershipBreed">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailOwnershipCurrentBadge">-</div>
                            <div class="mt-2" id="detailOwnershipTypeBadge">-</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="ownership-section-title">
                            <i class="fas fa-user-tie"></i> Propietario
                        </div>
                        <div class="ownership-detail-grid">
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Nombre / razon social</div>
                                <div class="ownership-detail-value" id="detailOwnershipOwner">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Documento</div>
                                <div class="ownership-detail-value" id="detailOwnershipOwnerDocument">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Telefono</div>
                                <div class="ownership-detail-value" id="detailOwnershipOwnerPhone">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Correo</div>
                                <div class="ownership-detail-value" id="detailOwnershipOwnerEmail">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="ownership-section-title">
                            <i class="fas fa-clipboard-list"></i> Datos del historial
                        </div>
                        <div class="ownership-detail-grid">
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Desde</div>
                                <div class="ownership-detail-value" id="detailOwnershipStartDate">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Hasta</div>
                                <div class="ownership-detail-value" id="detailOwnershipEndDate">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Documento referencia</div>
                                <div class="ownership-detail-value" id="detailOwnershipDocumentReference">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Precio</div>
                                <div class="ownership-detail-value" id="detailOwnershipPrice">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Fecha de registro</div>
                                <div class="ownership-detail-value" id="detailOwnershipCreatedAt">-</div>
                            </div>
                            <div class="ownership-detail-item">
                                <div class="ownership-detail-label">Ultima actualizacion</div>
                                <div class="ownership-detail-value" id="detailOwnershipUpdatedAt">-</div>
                            </div>
                            <div class="ownership-detail-item ownership-detail-item-wide">
                                <div class="ownership-detail-label">Observaciones</div>
                                <div class="ownership-detail-value" id="detailOwnershipNotes">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
