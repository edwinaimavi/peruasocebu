<div class="modal fade" id="contactMessageDetailModal" tabindex="-1" role="dialog"
    aria-labelledby="contactMessageDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-envelope-open text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="contactMessageDetailModalLabel">Detalle del Mensaje</h5>
                        <small class="text-muted" id="detailContactSubtitle">Mensaje recibido desde la web publica</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div class="contact-detail-hero p-3 mb-3">
                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                        <div>
                            <div class="contact-detail-label">Contacto</div>
                            <h3 id="detailContactName" class="mb-1">-</h3>
                            <div class="text-muted" id="detailContactSubject">-</div>
                        </div>
                        <div id="detailContactStatus" class="mt-2 mt-md-0">-</div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <a class="btn btn-success btn-sm d-none mr-2 mb-2" id="detailContactWhatsapp" href="#"
                            target="_blank" rel="noopener">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <a class="btn btn-outline-primary btn-sm d-none mr-2 mb-2" id="detailContactEmail" href="#">
                            <i class="fas fa-envelope mr-1"></i> Enviar correo
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2 mb-2 copyContactData"
                            data-copy-target="#detailContactPhone" data-copy-label="Telefono">
                            <i class="fas fa-phone mr-1"></i> Copiar telefono
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-2 mb-2 copyContactData"
                            data-copy-target="#detailContactEmailText" data-copy-label="Correo">
                            <i class="fas fa-at mr-1"></i> Copiar correo
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="contact-detail-label">Telefono</div>
                                <div class="contact-detail-value" id="detailContactPhone">-</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="contact-detail-label">Correo</div>
                                <div class="contact-detail-value" id="detailContactEmailText">-</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="contact-detail-label">Fecha de envio</div>
                                <div class="contact-detail-value" id="detailContactCreatedAt">-</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="contact-detail-label">Ultima actualizacion</div>
                                <div class="contact-detail-value" id="detailContactUpdatedAt">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-message-box" id="detailContactMessage">-</div>
            </div>

            <div class="modal-footer justify-content-between">
                <div>
                    @can('admin.contact-messages.update')
                        <button type="button" class="btn btn-outline-secondary btn-sm detailStatusAction"
                            data-action="mark-read">
                            <i class="fas fa-envelope-open mr-1"></i> Marcar leido
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm detailStatusAction"
                            data-action="mark-answered">
                            <i class="fas fa-check-double mr-1"></i> Marcar respondido
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm detailStatusAction"
                            data-action="mark-new">
                            <i class="fas fa-bell mr-1"></i> Marcar nuevo
                        </button>
                    @endcan
                    @can('admin.contact-messages.destroy')
                        <button type="button" class="btn btn-outline-danger btn-sm" id="detailDeleteContactMessage">
                            <i class="fas fa-trash mr-1"></i> Eliminar
                        </button>
                    @endcan
                </div>
                <button type="button" class="btn btn-light border" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
