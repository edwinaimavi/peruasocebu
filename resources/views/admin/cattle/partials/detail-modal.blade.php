<div class="modal fade" id="cattleDetailModal" tabindex="-1" role="dialog" aria-labelledby="cattleDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center min-w-0">
                    <div class="icon-circle bg-light mr-3 flex-shrink-0">
                        <i class="fas fa-paw text-secondary"></i>
                    </div>
                    <div class="min-w-0">
                        <h5 class="modal-title mb-0" id="cattleDetailModalLabel">Detalle del Ganado</h5>
                        <small class="text-muted" id="detailCattleSubtitle">Información registrada</small>
                    </div>
                </div>
                <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="cattle-detail-hero p-3 mb-3">
                    <div class="cattle-detail-photo-wrap">
                        <img id="detailMainPhoto" class="cattle-detail-photo d-none" src=""
                            alt="Foto principal del ganado">
                        <div id="detailMainPhotoPlaceholder" class="cattle-detail-photo-placeholder">
                            <i class="fas fa-paw"></i>
                        </div>
                    </div>

                    <div class="cattle-detail-info">
                        <div class="cattle-detail-label">Ganado</div>
                        <div class="cattle-detail-value cattle-detail-name h4 mb-0" id="detailName">—</div>
                        <div class="mt-2">
                            <span class="cattle-code-chip" id="detailCode">—</span>
                        </div>
                        <div class="cattle-detail-badges mt-3">
                            <span id="detailSexBadge">—</span>
                            <span id="detailPublicBadge">—</span>
                        </div>
                    </div>

                    <div class="cattle-detail-badges cattle-detail-status-badges">
                        <span id="detailStatusBadge">—</span>
                        <span id="detailSaleStatusBadge">—</span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3 cattle-detail-card">
                    <div class="card-body">
                        <div class="cattle-detail-grid">
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Raza</div>
                                <div class="cattle-detail-value" id="detailBreed">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Fecha de nacimiento</div>
                                <div class="cattle-detail-value" id="detailBirthDate">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Edad aproximada</div>
                                <div class="cattle-detail-value" id="detailAge">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Color</div>
                                <div class="cattle-detail-value" id="detailColor">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Criadero / Hacienda</div>
                                <div class="cattle-detail-value" id="detailRanch">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Propietario actual</div>
                                <div class="cattle-detail-value" id="detailOwner">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Pureza racial</div>
                                <div class="cattle-detail-value" id="detailPurity">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Peso</div>
                                <div class="cattle-detail-value" id="detailWeight">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Altura</div>
                                <div class="cattle-detail-value" id="detailHeight">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Arete / placa</div>
                                <div class="cattle-detail-value" id="detailEarTag">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Chip</div>
                                <div class="cattle-detail-value" id="detailChip">—</div>
                            </div>
                            <div class="cattle-detail-item cattle-detail-item-wide">
                                <div class="cattle-detail-label">Observaciones</div>
                                <div class="cattle-detail-value" id="detailObservations">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Fecha de registro</div>
                                <div class="cattle-detail-value" id="detailCreatedAt">—</div>
                            </div>
                            <div class="cattle-detail-item">
                                <div class="cattle-detail-label">Última actualización</div>
                                <div class="cattle-detail-value" id="detailUpdatedAt">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cattle-genealogy-card p-3">
                    <div class="cattle-section-title mb-3">
                        <i class="fas fa-dna"></i> Genealogía básica
                    </div>
                    <div class="cattle-genealogy-grid">
                        <div class="card border-0 shadow-sm cattle-genealogy-parent-card">
                            <div class="card-body">
                                <div class="cattle-detail-label">Padre</div>
                                <div class="cattle-detail-value font-weight-bold" id="detailFather">No registrado</div>
                                <div class="text-muted small mt-1 cattle-detail-value" id="detailFatherBreed">—</div>
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm cattle-genealogy-parent-card">
                            <div class="card-body">
                                <div class="cattle-detail-label">Madre</div>
                                <div class="cattle-detail-value font-weight-bold" id="detailMother">No registrado</div>
                                <div class="text-muted small mt-1 cattle-detail-value" id="detailMotherBreed">—</div>
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
