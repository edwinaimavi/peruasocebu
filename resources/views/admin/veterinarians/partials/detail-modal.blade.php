<div class="modal fade" id="veterinarianDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="veterinarianDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-notes-medical text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="veterinarianDetailModalLabel">Detalle del Veterinario</h5>
                        <small class="text-muted" id="detailVeterinarianSubtitle">Información registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="veterinarian-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="veterinarian-detail-label">Nombre completo</div>
                            <div class="veterinarian-detail-value h4 mb-0" id="detailFullName">—</div>
                            <div class="text-muted mt-1" id="detailProfessionalSummary">—</div>
                        </div>
                        <div class="col-md-5 text-md-right mt-3 mt-md-0">
                            <div id="detailStatus">—</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Tipo de documento</div>
                                <div class="veterinarian-detail-value mb-3" id="detailDocumentType">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Número de documento</div>
                                <div class="veterinarian-detail-value mb-3" id="detailDocumentNumber">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Colegiatura</div>
                                <div class="veterinarian-detail-value mb-3" id="detailLicenseNumber">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Especialidad</div>
                                <div class="veterinarian-detail-value mb-3" id="detailSpecialty">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="veterinarian-detail-label">Teléfono</div>
                                <div class="veterinarian-detail-value mb-3" id="detailPhone">—</div>
                            </div>
                            <div class="col-md-8">
                                <div class="veterinarian-detail-label">Correo</div>
                                <div class="veterinarian-detail-value mb-3" id="detailEmail">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="veterinarian-detail-label">Dirección</div>
                                <div class="veterinarian-detail-value mb-3" id="detailAddress">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="veterinarian-detail-label">Firma digital</div>
                                <div class="veterinarian-detail-signature-wrap mb-3">
                                    <img id="detailSignature" class="veterinarian-detail-signature d-none"
                                        src="" alt="Firma digital">
                                    <div id="detailSignaturePlaceholder"
                                        class="veterinarian-detail-signature-placeholder">
                                        <i class="fas fa-signature"></i>
                                        <span>Sin firma</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="veterinarian-detail-label">Observaciones</div>
                                <div class="veterinarian-detail-value mb-3" id="detailNotes">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Fecha de registro</div>
                                <div class="veterinarian-detail-value" id="detailCreatedAt">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="veterinarian-detail-label">Última actualización</div>
                                <div class="veterinarian-detail-value" id="detailUpdatedAt">—</div>
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
