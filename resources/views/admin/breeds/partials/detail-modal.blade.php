<div class="modal fade" id="breedDetailModal" tabindex="-1" role="dialog" aria-labelledby="breedDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-dna text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="breedDetailModalLabel">Detalle de la Raza</h5>
                        <small class="text-muted" id="detailBreedSubtitle">Información registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="breed-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="breed-detail-label">Raza</div>
                            <div class="breed-detail-value h4 mb-0" id="detailName">—</div>
                            <div class="mt-2">
                                <span class="breed-code-chip" id="detailCode">—</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-3 mt-md-0">
                            <div id="detailStatus">—</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="breed-detail-label">Imagen</div>
                                <div class="breed-detail-image mb-3">
                                    <img id="detailBreedImage" class="d-none" src="" alt="Imagen de la raza">
                                    <div id="detailBreedImagePlaceholder" class="breed-detail-image-placeholder">
                                        <i class="fas fa-cow"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="breed-detail-label">País de origen</div>
                                <div class="breed-detail-value mb-3" id="detailOriginCountry">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="breed-detail-label">Estado</div>
                                <div class="breed-detail-value mb-3" id="detailStatusText">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="breed-detail-label">Descripción</div>
                                <div class="breed-detail-value breed-detail-content mb-3" id="detailDescription">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="breed-detail-label">Características</div>
                                <div class="breed-detail-value breed-detail-content mb-3" id="detailCharacteristics">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="breed-detail-label">Fecha de registro</div>
                                <div class="breed-detail-value" id="detailCreatedAt">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="breed-detail-label">Última actualización</div>
                                <div class="breed-detail-value" id="detailUpdatedAt">—</div>
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
