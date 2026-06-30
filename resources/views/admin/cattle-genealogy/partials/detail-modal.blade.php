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
                        <h5 class="modal-title mb-0" id="genealogyDetailModalLabel">Detalle GenealÃ³gico</h5>
                        <small class="text-muted" id="detailGenealogySubtitle">InformaciÃ³n registrada</small>
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
                            <div class="genealogy-detail-value h4 mb-1" id="detailMainAnimal">â€”</div>
                            <div class="genealogy-detail-value text-muted" id="detailMainBreed">â€”</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailRelationBadge">â€”</div>
                            <div class="mt-2" id="detailGenerationBadge">â€”</div>
                        </div>
                    </div>
                </div>

                <div class="genealogy-flow-card p-3 mb-3">
                    <div class="genealogy-flow-grid">
                        <div class="genealogy-flow-node">
                            <div class="genealogy-detail-label">Animal principal</div>
                            <div class="genealogy-detail-value font-weight-bold" id="detailFlowMain">â€”</div>
                        </div>
                        <div class="genealogy-flow-relation">
                            <i class="fas fa-arrow-right mb-1"></i>
                            <span id="detailFlowRelation">â€”</span>
                        </div>
                        <div class="genealogy-flow-node">
                            <div class="genealogy-detail-label">Familiar</div>
                            <div class="genealogy-detail-value font-weight-bold" id="detailFlowRelative">â€”</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="genealogy-section-title">
                            <i class="fas fa-info-circle"></i> InformaciÃ³n completa
                        </div>
                        <div class="genealogy-detail-grid">
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Criadero</div>
                                <div class="genealogy-detail-value" id="detailMainRanch">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Propietario</div>
                                <div class="genealogy-detail-value" id="detailMainOwner">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Familiar registrado</div>
                                <div class="genealogy-detail-value" id="detailRegisteredRelative">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Ruta de linaje</div>
                                <div class="genealogy-detail-value" id="detailLineagePath">-</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Descripcion de relacion</div>
                                <div class="genealogy-detail-value" id="detailLineageDescription">-</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">CÃ³digo familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeCode">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Nombre familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeName">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Raza familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativeBreed">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Pureza familiar</div>
                                <div class="genealogy-detail-value" id="detailRelativePurity">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Fecha de registro</div>
                                <div class="genealogy-detail-value" id="detailGenealogyCreatedAt">â€”</div>
                            </div>
                            <div class="genealogy-detail-item">
                                <div class="genealogy-detail-label">Ãšltima actualizaciÃ³n</div>
                                <div class="genealogy-detail-value" id="detailGenealogyUpdatedAt">â€”</div>
                            </div>
                            <div class="genealogy-detail-item genealogy-detail-item-wide">
                                <div class="genealogy-detail-label">Observaciones</div>
                                <div class="genealogy-detail-value" id="detailNotes">â€”</div>
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
