<div class="modal fade" id="genealogyDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="genealogyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center min-w-0">
                    <div class="icon-circle bg-light mr-3 flex-shrink-0">
                        <i class="fas fa-sitemap text-secondary"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="modal-title mb-0" id="genealogyDetailModalLabel">Detalle Genealógico</h5>
                        <small class="text-muted" id="detailGenealogySubtitle">Información registrada</small>
                    </div>
                </div>
                <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="genealogy-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-3 mb-md-0">
                            <div class="genealogy-detail-photo-wrap mx-auto">
                                <img id="detailGenealogyPhoto" class="genealogy-detail-photo d-none" src=""
                                    alt="Foto del animal principal">
                                <div id="detailGenealogyPhotoPlaceholder" class="genealogy-detail-photo-placeholder">
                                    <i class="fas fa-paw"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="genealogy-detail-label">Animal principal</div>
                            <div class="genealogy-detail-value h4 mb-1" id="detailMainAnimal">—</div>
                            <div class="genealogy-detail-value text-muted" id="detailMainBreed">—</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailRelationBadge">—</div>
                            <div class="mt-2" id="detailGenerationBadge">—</div>
                        </div>
                    </div>
                </div>

                <div class="genealogy-flow-card p-3 mb-3">
                    <div class="genealogy-flow-grid">
                        <div class="genealogy-flow-node">
                            <div class="genealogy-detail-label">Animal principal</div>
                            <div class="genealogy-detail-value font-weight-bold" id="detailFlowMain">—</div>
                        </div>
                        <div class="genealogy-flow-relation">
                            <i class="fas fa-arrow-right mb-1"></i>
                            <span id="detailFlowRelation">—</span>
                        </div>
                        <div class="genealogy-flow-node">
                            <div class="genealogy-detail-label">Familiar</div>
                            <div class="genealogy-detail-value font-weight-bold" id="detailFlowRelative">—</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="genealogy-section-title">
                            <i class="fas fa-info-circle"></i> Información completa
                        </div>
                        <div class="genealogy-detail-grid">
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Criadero</div>
                                <div class="genealogy-detail-value" id="detailMainRanch">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Propietario</div>
                                <div class="genealogy-detail-value" id="detailMainOwner">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Familiar registrado</div>
                                <div class="genealogy-detail-value" id="detailRegisteredRelative">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Código familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeCode">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Nombre familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeName">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Raza familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeBreed">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Pureza familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativePurity">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Fecha de registro</div>
                                <div class="genealogy-detail-value" id="detailGenealogyCreatedAt">—</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Última actualización</div>
                                <div class="genealogy-detail-value" id="detailGenealogyUpdatedAt">—</div>
                            </div>
                            <div class="genealogy-detail-item genealogy-detail-item-wide">
                                <div class="genealogy-detail-label">Observaciones</div>
                                <div class="genealogy-detail-value" id="detailNotes">—</div>
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
