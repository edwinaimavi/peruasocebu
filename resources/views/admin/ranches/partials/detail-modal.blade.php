<div class="modal fade" id="ranchDetailModal" tabindex="-1" role="dialog" aria-labelledby="ranchDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-warehouse text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="ranchDetailModalLabel">Detalle del Criadero / Hacienda</h5>
                        <small class="text-muted" id="detailRanchSubtitle">Información institucional registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="ranch-detail-label">Nombre</div>
                                <div class="ranch-detail-value h5 mb-3" id="detailName">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="ranch-detail-label">Estado</div>
                                <div class="ranch-detail-value mb-3" id="detailStatus">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="ranch-detail-label">Razón social</div>
                                <div class="ranch-detail-value mb-3" id="detailBusinessName">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="ranch-detail-label">Documento</div>
                                <div class="ranch-detail-value mb-3" id="detailDocument">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="ranch-detail-label">Dirección completa</div>
                                <div class="ranch-detail-value mb-3" id="detailAddress">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="ranch-detail-label">Teléfono</div>
                                <div class="ranch-detail-value mb-3" id="detailPhone">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="ranch-detail-label">Correo</div>
                                <div class="ranch-detail-value mb-3" id="detailEmail">—</div>
                            </div>
                            <div class="col-md-4">
                                <div class="ranch-detail-label">Responsable</div>
                                <div class="ranch-detail-value mb-3" id="detailRepresentative">—</div>
                            </div>
                            <div class="col-md-12">
                                <div class="ranch-detail-label">Descripción</div>
                                <div class="ranch-detail-value mb-3" id="detailDescription">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="ranch-detail-label">Fecha de registro</div>
                                <div class="ranch-detail-value" id="detailCreatedAt">—</div>
                            </div>
                            <div class="col-md-6">
                                <div class="ranch-detail-label">Última actualización</div>
                                <div class="ranch-detail-value" id="detailUpdatedAt">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    @foreach ([
                        ['id' => 'detailLogo', 'empty' => 'detailLogoEmpty', 'label' => 'Logo'],
                        ['id' => 'detailSeal', 'empty' => 'detailSealEmpty', 'label' => 'Sello'],
                        ['id' => 'detailSignature', 'empty' => 'detailSignatureEmpty', 'label' => 'Firma'],
                    ] as $media)
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="ranch-detail-label">{{ $media['label'] }}</div>
                                    <img class="ranch-detail-media d-none" id="{{ $media['id'] }}"
                                        alt="{{ $media['label'] }} del criadero">
                                    <div class="text-muted py-5" id="{{ $media['empty'] }}">
                                        <i class="far fa-image fa-2x mb-2"></i>
                                        <div class="small">No registrado</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
