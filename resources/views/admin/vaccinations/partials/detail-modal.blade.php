<div class="modal fade" id="vaccinationDetailModal" tabindex="-1" role="dialog" aria-labelledby="vaccinationDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-syringe text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="vaccinationDetailModalLabel">Detalle de Vacuna</h5>
                        <small class="text-muted" id="detailVaccinationSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="vaccination-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="vaccination-detail-photo-wrap mx-auto">
                                <img id="detailVaccinationCattlePhoto" class="vaccination-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailVaccinationCattlePhotoPlaceholder" class="vaccination-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="vaccination-detail-label">Ganado</div>
                            <div class="vaccination-detail-value h4 mb-0" id="detailVaccinationCattle">-</div>
                            <div class="text-muted mt-1" id="detailVaccinationBreed">-</div>
                            <div class="text-muted mt-1" id="detailVaccinationOwner">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailNextDueBadge">-</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="vaccination-section-title"><i class="fas fa-shield-alt"></i> Datos de vacuna</div>
                        <div class="vaccination-detail-grid">
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Vacuna</div><div class="vaccination-detail-value" id="detailVaccineName">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Dosis</div><div class="vaccination-detail-value" id="detailDose">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Lote</div><div class="vaccination-detail-value" id="detailBatchNumber">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Fecha aplicada</div><div class="vaccination-detail-value" id="detailApplicationDate">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Proxima dosis</div><div class="vaccination-detail-value" id="detailNextDueDate">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Veterinario</div><div class="vaccination-detail-value" id="detailVaccinationVeterinarian">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Colegiatura</div><div class="vaccination-detail-value" id="detailVaccinationLicense">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Fecha de registro</div><div class="vaccination-detail-value" id="detailVaccinationCreatedAt">-</div></div>
                            <div class="vaccination-detail-item"><div class="vaccination-detail-label">Ultima actualizacion</div><div class="vaccination-detail-value" id="detailVaccinationUpdatedAt">-</div></div>
                            <div class="vaccination-detail-item vaccination-detail-item-wide"><div class="vaccination-detail-label">Observaciones</div><div class="vaccination-detail-value" id="detailVaccinationObservations">-</div></div>
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
