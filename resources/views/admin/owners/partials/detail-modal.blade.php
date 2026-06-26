<div class="modal fade" id="ownerDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="ownerDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-address-card text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ownerDetailModalLabel">Detalle del Propietario</h5>
                        <small class="text-muted" id="detailOwnerSubtitle">Información registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="owner-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="owner-detail-photo-wrap mx-auto">
                                <img id="detailPhoto" class="owner-detail-photo d-none" src=""
                                    alt="Foto del propietario">
                                <div id="detailPhotoPlaceholder" class="owner-detail-photo-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="owner-detail-label">Nombre / razón social</div>
                            <div class="owner-detail-value h4 mb-0" id="detailDisplayName">—</div>
                            <div class="text-muted mt-1" id="detailContactName">—</div>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <div id="detailOwnerType">—</div>
                            <div class="mt-2" id="detailStatus">—</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="owner-detail-label">Tipo de documento</div>
                                <div class="owner-detail-value mb-3" id="detailDocumentType">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="owner-detail-label">Número de documento</div>
                                <div class="owner-detail-value mb-3" id="detailDocumentNumber">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="owner-detail-label">Teléfono</div>
                                <div class="owner-detail-value mb-3" id="detailPhone">—</div>
                            </div>
                            <div class="col-md-8">
                                <div class="owner-detail-label">Correo</div>
                                <div class="owner-detail-value mb-3" id="detailEmail">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="owner-detail-label">Dirección</div>
                                <div class="owner-detail-value mb-3" id="detailAddress">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="owner-detail-label">Observaciones</div>
                                <div class="owner-detail-value mb-3" id="detailNotes">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="owner-detail-label">Fecha de registro</div>
                                <div class="owner-detail-value" id="detailCreatedAt">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="owner-detail-label">Última actualización</div>
                                <div class="owner-detail-value" id="detailUpdatedAt">—</div>
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
