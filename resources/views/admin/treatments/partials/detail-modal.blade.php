<div class="modal fade" id="treatmentDetailModal" tabindex="-1" role="dialog" aria-labelledby="treatmentDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-pills text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="treatmentDetailModalLabel">Detalle de Tratamiento</h5>
                        <small class="text-muted" id="detailTreatmentSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="treatment-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="treatment-detail-photo-wrap mx-auto">
                                <img id="detailTreatmentCattlePhoto" class="treatment-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailTreatmentCattlePhotoPlaceholder" class="treatment-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="treatment-detail-label">Ganado</div>
                            <div class="treatment-detail-value h4 mb-0" id="detailTreatmentCattle">-</div>
                            <div class="text-muted mt-1" id="detailTreatmentBreed">-</div>
                            <div class="text-muted mt-1" id="detailTreatmentOwner">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailTreatmentVeterinarianBadge">-</div>
                            <div id="detailTreatmentDurationBadge" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="treatment-section-title"><i class="fas fa-prescription-bottle-alt"></i> Datos del tratamiento</div>
                        <div class="treatment-detail-grid">
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Tratamiento</div><div class="treatment-detail-value" id="detailTreatmentName">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Fecha</div><div class="treatment-detail-value" id="detailTreatmentDate">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Medicamento</div><div class="treatment-detail-value" id="detailTreatmentMedicine">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Dosis</div><div class="treatment-detail-value" id="detailTreatmentDose">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Duracion</div><div class="treatment-detail-value" id="detailTreatmentDuration">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Veterinario</div><div class="treatment-detail-value" id="detailTreatmentVeterinarian">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Colegiatura</div><div class="treatment-detail-value" id="detailTreatmentLicense">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Especialidad</div><div class="treatment-detail-value" id="detailTreatmentSpecialty">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="treatment-section-title"><i class="fas fa-clipboard-list"></i> Detalle clinico</div>
                        <div class="treatment-detail-grid">
                            <div class="treatment-detail-item treatment-detail-item-wide"><div class="treatment-detail-label">Motivo</div><div class="treatment-detail-value" id="detailTreatmentReason">-</div></div>
                            <div class="treatment-detail-item treatment-detail-item-wide"><div class="treatment-detail-label">Observaciones</div><div class="treatment-detail-value" id="detailTreatmentObservations">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Fecha de registro</div><div class="treatment-detail-value" id="detailTreatmentCreatedAt">-</div></div>
                            <div class="treatment-detail-item"><div class="treatment-detail-label">Ultima actualizacion</div><div class="treatment-detail-value" id="detailTreatmentUpdatedAt">-</div></div>
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
