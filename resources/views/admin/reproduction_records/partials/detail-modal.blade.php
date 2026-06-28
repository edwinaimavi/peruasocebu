<div class="modal fade" id="reproductionRecordDetailModal" tabindex="-1" role="dialog" aria-labelledby="reproductionRecordDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-venus-mars text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="reproductionRecordDetailModalLabel">Detalle Reproductivo</h5>
                        <small class="text-muted" id="detailReproductionSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="reproduction-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="reproduction-detail-photo-wrap mx-auto">
                                <img id="detailReproductionCattlePhoto" class="reproduction-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailReproductionCattlePhotoPlaceholder" class="reproduction-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="reproduction-detail-label">Animal principal</div>
                            <div class="reproduction-detail-value h4 mb-0" id="detailReproductionCattle">-</div>
                            <div class="text-muted mt-1" id="detailReproductionCattleMeta">-</div>
                            <div class="text-muted mt-1" id="detailReproductionOwner">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailReproductionMethodBadge">-</div>
                            <div id="detailReproductionResultBadge" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="reproduction-section-title"><i class="fas fa-venus-mars"></i> Animales vinculados</div>
                        <div class="reproduction-detail-grid">
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Pareja</div><div class="reproduction-detail-value" id="detailReproductionPartner">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Datos de pareja</div><div class="reproduction-detail-value" id="detailReproductionPartnerMeta">-</div></div>
                            <div class="reproduction-detail-item reproduction-detail-item-wide"><div class="reproduction-detail-label">Cria nacida</div><div class="reproduction-detail-value" id="detailReproductionOffspring">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="reproduction-section-title"><i class="fas fa-heartbeat"></i> Datos reproductivos</div>
                        <div class="reproduction-detail-grid">
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Metodo</div><div class="reproduction-detail-value" id="detailReproductionMethod">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Fecha reproductiva</div><div class="reproduction-detail-value" id="detailReproductionDate">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Control de prenez</div><div class="reproduction-detail-value" id="detailPregnancyCheckDate">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Resultado</div><div class="reproduction-detail-value" id="detailPregnancyResult">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Fecha de parto</div><div class="reproduction-detail-value" id="detailBirthDate">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Estado parto</div><div class="reproduction-detail-value" id="detailBirthBadge">-</div></div>
                            <div class="reproduction-detail-item reproduction-detail-item-wide"><div class="reproduction-detail-label">Observaciones</div><div class="reproduction-detail-value" id="detailReproductionObservations">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Fecha de registro</div><div class="reproduction-detail-value" id="detailReproductionCreatedAt">-</div></div>
                            <div class="reproduction-detail-item"><div class="reproduction-detail-label">Ultima actualizacion</div><div class="reproduction-detail-value" id="detailReproductionUpdatedAt">-</div></div>
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
