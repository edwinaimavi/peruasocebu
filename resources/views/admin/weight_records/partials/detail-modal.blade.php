<div class="modal fade" id="weightRecordDetailModal" tabindex="-1" role="dialog" aria-labelledby="weightRecordDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-weight text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="weightRecordDetailModalLabel">Detalle de Pesaje</h5>
                        <small class="text-muted" id="detailWeightRecordSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="weight-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="weight-detail-photo-wrap mx-auto">
                                <img id="detailWeightCattlePhoto" class="weight-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailWeightCattlePhotoPlaceholder" class="weight-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="weight-detail-label">Ganado</div>
                            <div class="weight-detail-value h4 mb-0" id="detailWeightCattle">-</div>
                            <div class="text-muted mt-1" id="detailWeightBreed">-</div>
                            <div class="text-muted mt-1" id="detailWeightOwner">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailWeightBadge">-</div>
                            <div id="detailBodyConditionBadge" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="weight-section-title"><i class="fas fa-balance-scale"></i> Datos del pesaje</div>
                        <div class="weight-detail-grid">
                            <div class="weight-detail-item"><div class="weight-detail-label">Peso</div><div class="weight-detail-value" id="detailWeightKg">-</div></div>
                            <div class="weight-detail-item"><div class="weight-detail-label">Fecha de pesaje</div><div class="weight-detail-value" id="detailWeightRecordDate">-</div></div>
                            <div class="weight-detail-item"><div class="weight-detail-label">Condicion corporal</div><div class="weight-detail-value" id="detailWeightBodyCondition">-</div></div>
                            <div class="weight-detail-item"><div class="weight-detail-label">Peso anterior</div><div class="weight-detail-value" id="detailPreviousWeight">-</div></div>
                            <div class="weight-detail-item weight-detail-item-wide"><div class="weight-detail-label">Evolucion</div><div class="weight-detail-value" id="detailWeightEvolution">-</div></div>
                            <div class="weight-detail-item weight-detail-item-wide"><div class="weight-detail-label">Observaciones</div><div class="weight-detail-value" id="detailWeightObservations">-</div></div>
                            <div class="weight-detail-item"><div class="weight-detail-label">Fecha de registro</div><div class="weight-detail-value" id="detailWeightCreatedAt">-</div></div>
                            <div class="weight-detail-item"><div class="weight-detail-label">Ultima actualizacion</div><div class="weight-detail-value" id="detailWeightUpdatedAt">-</div></div>
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
