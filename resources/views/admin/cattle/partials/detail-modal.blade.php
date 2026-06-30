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
                <div class="cattle-detail-tabs-wrapper">
                    <ul class="nav nav-tabs cattle-detail-tabs" id="cattleDetailTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="summary-tab" data-toggle="tab" href="#cattleSummaryTab" role="tab">
                                <i class="fas fa-info-circle"></i> Resumen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="genealogy-tab" data-toggle="tab" href="#cattleGenealogyTab" role="tab">
                                <i class="fas fa-sitemap"></i> Genealogía
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ownership-tab" data-toggle="tab" href="#cattleOwnershipTab" role="tab">
                                <i class="fas fa-history"></i> Propiedad y ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="health-tab" data-toggle="tab" href="#cattleHealthTab" role="tab">
                                <i class="fas fa-notes-medical"></i> Sanidad
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="production-tab" data-toggle="tab" href="#cattleProductionTab" role="tab">
                                <i class="fas fa-chart-line"></i> Producción y reproducción
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#cattleDocumentsTab" role="tab">
                                <i class="fas fa-certificate"></i> Certificados y fotos
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content cattle-detail-tab-content" id="cattleDetailTabContent">
                    <div class="tab-pane fade show active" id="cattleSummaryTab" role="tabpanel">
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
                    </div>

                    <div class="tab-pane fade" id="cattleGenealogyTab" role="tabpanel">

                <div class="cattle-genealogy-card p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-dna"></i> Árbol genealógico
                        </div>
                        @can('admin.cattle-genealogy.store')
                            <div class="cattle-detail-tab-actions">
                                <a class="btn btn-outline-primary btn-xs d-none" id="detailAddFatherLink" href="#">
                                    <i class="fas fa-plus mr-1"></i> Agregar padre
                                </a>
                                <a class="btn btn-outline-primary btn-xs d-none" id="detailAddMotherLink" href="#">
                                    <i class="fas fa-plus mr-1"></i> Agregar madre
                                </a>
                            </div>
                        @endcan
                    </div>
                    <div id="cattleGenealogyTree" class="cattle-genealogy-tree"></div>
                    <div class="cattle-genealogy-grid">
                        <div class="card border-0 shadow-sm cattle-genealogy-parent-card">
                            <div class="card-body">
                                <div class="cattle-detail-label">Padre</div>
                                <div class="cattle-detail-value font-weight-bold" id="detailFather">No registrado</div>
                                <div class="text-muted small mt-1 cattle-detail-value" id="detailFatherBreed">—</div>
                                @can('admin.cattle-genealogy.store')
                                    <a class="btn btn-outline-primary btn-xs mt-3 d-none" id="detailAddFatherLinkFallback" href="#">
                                        <i class="fas fa-plus mr-1"></i> Agregar padre
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <div class="card border-0 shadow-sm cattle-genealogy-parent-card">
                            <div class="card-body">
                                <div class="cattle-detail-label">Madre</div>
                                <div class="cattle-detail-value font-weight-bold" id="detailMother">No registrado</div>
                                <div class="text-muted small mt-1 cattle-detail-value" id="detailMotherBreed">—</div>
                                @can('admin.cattle-genealogy.store')
                                    <a class="btn btn-outline-primary btn-xs mt-3 d-none" id="detailAddMotherLinkFallback" href="#">
                                        <i class="fas fa-plus mr-1"></i> Agregar madre
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
                    </div>

                    <div class="tab-pane fade" id="cattleOwnershipTab" role="tabpanel">

                <div class="cattle-genealogy-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-history"></i> Historial de propietarios
                        </div>
                        @can('admin.ownership-histories.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddOwnershipHistoryLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Agregar historial
                            </a>
                        @endcan
                    </div>
                    <div id="detailOwnershipHistoryList" class="cattle-detail-grid"></div>
                    <div id="detailOwnershipHistoryEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-user-clock fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene historial de propietarios registrado.</div>
                    </div>
                </div>

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-handshake"></i> Ventas
                        </div>
                        @can('admin.cattle-sales.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddCattleSaleLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Registrar venta
                            </a>
                        @endcan
                    </div>
                    <div id="detailCattleSalesList" class="cattle-detail-grid"></div>
                    <div id="detailCattleSalesEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene ventas registradas.</div>
                    </div>
                </div>
                    </div>

                    <div class="tab-pane fade" id="cattleHealthTab" role="tabpanel">

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-notes-medical"></i> Revisiones veterinarias
                        </div>
                        @can('admin.veterinary-records.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddVeterinaryRecordLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nueva revision
                            </a>
                        @endcan
                    </div>
                    <div id="detailVeterinaryRecordsList" class="cattle-detail-grid"></div>
                    <div id="detailVeterinaryRecordsEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-stethoscope fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene revisiones veterinarias registradas.</div>
                    </div>
                </div>

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-syringe"></i> Vacunas
                        </div>
                        @can('admin.vaccinations.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddVaccinationLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nueva vacuna
                            </a>
                        @endcan
                    </div>
                    <div id="detailVaccinationsList" class="cattle-detail-grid"></div>
                    <div id="detailVaccinationsEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-syringe fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene vacunas registradas.</div>
                    </div>
                </div>

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-pills"></i> Tratamientos medicos
                        </div>
                        @can('admin.treatments.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddTreatmentLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nuevo tratamiento
                            </a>
                        @endcan
                    </div>
                    <div id="detailTreatmentsList" class="cattle-detail-grid"></div>
                    <div id="detailTreatmentsEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-pills fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene tratamientos medicos registrados.</div>
                    </div>
                </div>
                    </div>

                    <div class="tab-pane fade" id="cattleProductionTab" role="tabpanel">

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-weight"></i> Historial de pesajes
                        </div>
                        @can('admin.weight-records.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddWeightRecordLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nuevo pesaje
                            </a>
                        @endcan
                    </div>
                    <div id="detailLatestWeightSummary" class="alert alert-success py-2 px-3 d-none"></div>
                    <div id="detailWeightRecordsList" class="cattle-detail-grid"></div>
                    <div id="detailWeightRecordsEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-balance-scale fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene pesajes registrados.</div>
                    </div>
                </div>

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-venus-mars"></i> Historial reproductivo
                        </div>
                        @can('admin.reproduction-records.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddReproductionRecordLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nuevo registro reproductivo
                            </a>
                        @endcan
                    </div>
                    <div id="detailReproductionRecordsList" class="cattle-detail-grid"></div>
                    <div id="detailReproductionRecordsEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-venus-mars fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene registros reproductivos.</div>
                    </div>

                    <div class="cattle-section-title mt-3 mb-3">
                        <i class="fas fa-mars"></i> Participaciones como reproductor
                    </div>
                    <div id="detailReproductionPartnerList" class="cattle-detail-grid"></div>
                    <div id="detailReproductionPartnerEmpty" class="text-center text-muted py-3 d-none">
                        <div>No registra participaciones como reproductor.</div>
                    </div>
                </div>
                    </div>

                    <div class="tab-pane fade" id="cattleDocumentsTab" role="tabpanel">

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-certificate"></i> Certificados
                        </div>
                        @can('admin.certificates.store')
                            <a class="btn btn-outline-primary btn-sm" id="detailAddCertificateLink" href="#">
                                <i class="fas fa-plus mr-1"></i> Nuevo certificado
                            </a>
                        @endcan
                    </div>
                    <div id="detailCertificatesList" class="cattle-detail-grid"></div>
                    <div id="detailCertificatesEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-certificate fa-2x mb-2"></i>
                        <div>Este ganado aun no tiene certificados registrados.</div>
                    </div>
                </div>

                <div class="cattle-gallery-card p-3 mt-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                        <div class="cattle-section-title mb-0">
                            <i class="fas fa-images"></i> Galería de fotos
                        </div>
                        @can('admin.cattle.update')
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnOpenAddCattlePhoto">
                                <i class="fas fa-plus mr-1"></i> Agregar foto
                            </button>
                        @endcan
                    </div>
                    <div id="cattlePhotoGallery" class="cattle-photo-gallery-grid"></div>
                    <div id="cattlePhotoGalleryEmpty" class="text-center text-muted py-4 d-none">
                        <i class="fas fa-camera fa-2x mb-2"></i>
                        <div>Este ganado aún no tiene fotos registradas.</div>
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

<div class="modal fade" id="cattlePhotoFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header">
                <h5 class="modal-title" id="cattlePhotoFormTitle">Agregar foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="cattlePhotoForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div id="cattle-photo-error-messages" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-secondary" for="photo_image">Imagen</label>
                        <input class="form-control-file" id="photo_image" name="image" type="file"
                            accept="image/jpeg,image/png,image/webp">
                        <div class="invalid-feedback d-block" id="image-error"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label class="small font-weight-bold text-secondary" for="photo_title">Título</label>
                            <input class="form-control form-control-sm" id="photo_title" name="title" type="text" maxlength="255">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="small font-weight-bold text-secondary" for="photo_sort_order">Orden</label>
                            <input class="form-control form-control-sm" id="photo_sort_order" name="sort_order" type="number" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-secondary" for="photo_description">Descripción</label>
                        <textarea class="form-control form-control-sm" id="photo_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="custom-control custom-switch">
                        <input class="custom-control-input" id="photo_is_main" name="is_main" type="checkbox" value="1">
                        <label class="custom-control-label small font-weight-bold text-secondary" for="photo_is_main">
                            Marcar como principal
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="saveCattlePhotoButton">
                        <i class="fas fa-save mr-1"></i> Guardar foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cattlePhotoViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header">
                <h5 class="modal-title" id="photoViewTitle">Foto del ganado</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="photoViewImage" class="img-fluid rounded w-100" src="" alt="Foto del ganado">
                <div class="mt-3">
                    <span id="photoViewMainBadge"></span>
                    <p class="text-muted mb-0" id="photoViewDescription"></p>
                </div>
            </div>
        </div>
    </div>
</div>
