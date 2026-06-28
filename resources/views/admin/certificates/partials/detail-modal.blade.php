<div class="modal fade" id="certificateDetailModal" tabindex="-1" role="dialog" aria-labelledby="certificateDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-qrcode text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="certificateDetailModalLabel">Detalle de Certificado</h5>
                        <small class="text-muted" id="detailCertificateSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="certificate-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="certificate-detail-label">Certificado</div>
                            <div class="h4 mb-1" id="detailCertificateNumber">-</div>
                            <div class="text-muted">Codigo: <span id="detailVerificationCode">-</span></div>
                            <div class="mt-2" id="detailCertificateBadges">-</div>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <img id="detailQrImage" class="certificate-qr d-none" src="" alt="Codigo QR">
                            <div id="detailQrPending" class="badge badge-secondary">QR pendiente</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a class="btn btn-outline-primary btn-sm d-none" id="detailPdfLink" href="#" target="_blank" rel="noopener">
                            <i class="fas fa-file-pdf mr-1"></i> Ver PDF
                        </a>
                        <a class="btn btn-outline-success btn-sm" id="detailVerifyLink" href="#" target="_blank" rel="noopener">
                            <i class="fas fa-check-circle mr-1"></i> Verificar en linea
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="certificate-section-title"><i class="fas fa-award"></i> Datos del certificado</div>
                        <div class="certificate-detail-grid">
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Tipo</div><div class="certificate-detail-value" id="detailType">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Fecha emision</div><div class="certificate-detail-value" id="detailIssueDate">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Estado</div><div class="certificate-detail-value" id="detailStatus">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Pureza certificada</div><div class="certificate-detail-value" id="detailPurity">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="certificate-section-title"><i class="fas fa-paw"></i> Ganado certificado</div>
                        <div class="d-flex align-items-center mb-3">
                            <img id="detailCattlePhoto" class="certificate-photo d-none mr-3" src="" alt="Foto del ganado">
                            <div>
                                <div class="font-weight-bold" id="detailCattleLabel">-</div>
                                <div class="text-muted small" id="detailCattleBreed">-</div>
                            </div>
                        </div>
                        <div class="certificate-detail-grid">
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Codigo</div><div class="certificate-detail-value" id="detailCattleCode">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Sexo</div><div class="certificate-detail-value" id="detailCattleSex">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Nacimiento</div><div class="certificate-detail-value" id="detailCattleBirthDate">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Pureza registrada</div><div class="certificate-detail-value" id="detailCattlePurity">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="certificate-section-title"><i class="fas fa-link"></i> Entidades</div>
                        <div class="certificate-detail-grid">
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Criadero</div><div class="certificate-detail-value" id="detailRanch">-</div><div class="text-muted small" id="detailRanchData"></div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Propietario</div><div class="certificate-detail-value" id="detailOwner">-</div><div class="text-muted small" id="detailOwnerData"></div></div>
                            <div class="certificate-detail-item certificate-detail-item-wide"><div class="certificate-detail-label">Veterinario / certificador</div><div class="certificate-detail-value" id="detailVeterinarian">-</div><div class="text-muted small" id="detailVeterinarianData"></div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                            <div class="certificate-section-title mb-0">
                                <i class="fas fa-signature"></i> Firmas y sellos
                            </div>
                            @can('admin.certificate-signatures.store')
                                <a class="btn btn-outline-primary btn-sm" id="detailAddCertificateSignatureLink" href="#">
                                    <i class="fas fa-plus mr-1"></i> Agregar firma
                                </a>
                            @endcan
                        </div>
                        <div id="detailCertificateSignaturesList" class="certificate-detail-grid"></div>
                        <div id="detailCertificateSignaturesEmpty" class="text-center text-muted py-4 d-none">
                            <i class="fas fa-signature fa-2x mb-2"></i>
                            <div>Este certificado aun no tiene firmas o sellos registrados.</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="certificate-section-title"><i class="fas fa-clipboard-list"></i> Registro</div>
                        <div class="certificate-detail-grid">
                            <div class="certificate-detail-item certificate-detail-item-wide"><div class="certificate-detail-label">Observaciones</div><div class="certificate-detail-value" id="detailObservations">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Fecha de registro</div><div class="certificate-detail-value" id="detailCreatedAt">-</div></div>
                            <div class="certificate-detail-item"><div class="certificate-detail-label">Ultima actualizacion</div><div class="certificate-detail-value" id="detailUpdatedAt">-</div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
