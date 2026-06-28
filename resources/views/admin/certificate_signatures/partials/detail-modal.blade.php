<div class="modal fade" id="certificateSignatureDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="certificateSignatureDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-stamp text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="certificateSignatureDetailModalLabel">Detalle de Firma</h5>
                        <small class="text-muted" id="detailSignatureSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="signature-section-title"><i class="fas fa-certificate"></i> Datos del certificado</div>
                        <div class="signature-detail-grid">
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Nro. certificado</div>
                                <div class="signature-detail-value" id="detailSignatureCertificateNumber">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Codigo de verificacion</div>
                                <div class="signature-detail-value" id="detailSignatureVerificationCode">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Ganado certificado</div>
                                <div class="signature-detail-value" id="detailSignatureCattle">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Tipo y estado</div>
                                <div class="signature-detail-value" id="detailSignatureCertificateMeta">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="signature-section-title"><i class="fas fa-user-tie"></i> Datos de la firma</div>
                        <div class="signature-detail-grid">
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Tipo de persona</div>
                                <div class="signature-detail-value" id="detailSignaturePersonType">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Nombre</div>
                                <div class="signature-detail-value" id="detailSignaturePersonName">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Cargo</div>
                                <div class="signature-detail-value" id="detailSignaturePosition">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Registro</div>
                                <div class="signature-detail-value" id="detailSignatureCreatedAt">-</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Ultima actualizacion</div>
                                <div class="signature-detail-value" id="detailSignatureUpdatedAt">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="signature-section-title"><i class="fas fa-images"></i> Firma y sello</div>
                        <div class="signature-detail-grid">
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Firma</div>
                                <div id="detailSignatureImageWrap">Sin firma registrada</div>
                            </div>
                            <div class="signature-detail-item">
                                <div class="signature-detail-label">Sello</div>
                                <div id="detailSealImageWrap">Sin sello registrado</div>
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
