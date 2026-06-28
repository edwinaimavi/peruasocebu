<div class="modal fade" id="veterinaryRecordDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="veterinaryRecordDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-notes-medical text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="veterinaryRecordDetailModalLabel">Detalle de Revision</h5>
                        <small class="text-muted" id="detailVeterinarySubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <div class="veterinary-detail-hero p-3 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center mb-3 mb-md-0">
                            <div class="veterinary-detail-photo-wrap mx-auto">
                                <img id="detailVeterinaryCattlePhoto" class="veterinary-detail-photo d-none" src="" alt="Foto del ganado">
                                <div id="detailVeterinaryCattlePhotoPlaceholder" class="veterinary-detail-photo-placeholder"><i class="fas fa-paw"></i></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="veterinary-detail-label">Ganado</div>
                            <div class="veterinary-detail-value h4 mb-0" id="detailVeterinaryCattle">-</div>
                            <div class="text-muted mt-1" id="detailVeterinaryBreed">-</div>
                            <div class="text-muted mt-1" id="detailVeterinaryOwner">-</div>
                        </div>
                        <div class="col-md-3 text-md-right mt-3 mt-md-0">
                            <div id="detailRecordTypeBadge">-</div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="veterinary-section-title"><i class="fas fa-user-md"></i> Datos veterinarios</div>
                        <div class="veterinary-detail-grid">
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Veterinario</div><div class="veterinary-detail-value" id="detailVeterinarian">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Colegiatura</div><div class="veterinary-detail-value" id="detailVeterinarianLicense">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Especialidad</div><div class="veterinary-detail-value" id="detailVeterinarianSpecialty">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Fecha atencion</div><div class="veterinary-detail-value" id="detailRecordDate">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Proxima visita</div><div class="veterinary-detail-value" id="detailNextVisitDate">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Documento</div><div class="veterinary-detail-value" id="detailVeterinaryDocument">-</div></div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="veterinary-section-title"><i class="fas fa-clipboard-list"></i> Detalle clinico</div>
                        <div class="veterinary-detail-grid">
                            <div class="veterinary-detail-item veterinary-detail-item-wide"><div class="veterinary-detail-label">Diagnostico</div><div class="veterinary-detail-value" id="detailDiagnosis">-</div></div>
                            <div class="veterinary-detail-item veterinary-detail-item-wide"><div class="veterinary-detail-label">Tratamiento indicado</div><div class="veterinary-detail-value" id="detailTreatment">-</div></div>
                            <div class="veterinary-detail-item veterinary-detail-item-wide"><div class="veterinary-detail-label">Observaciones</div><div class="veterinary-detail-value" id="detailObservations">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Fecha de registro</div><div class="veterinary-detail-value" id="detailVeterinaryCreatedAt">-</div></div>
                            <div class="veterinary-detail-item"><div class="veterinary-detail-label">Ultima actualizacion</div><div class="veterinary-detail-value" id="detailVeterinaryUpdatedAt">-</div></div>
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
