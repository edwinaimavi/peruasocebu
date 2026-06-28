<div class="modal fade" id="blogPostDetailModal" tabindex="-1" role="dialog" aria-labelledby="blogPostDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3"><i class="fas fa-eye text-secondary"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="blogPostDetailModalLabel">Detalle de Publicacion</h5>
                        <small class="text-muted" id="detailBlogSubtitle">Informacion registrada</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="blog-detail-hero p-3 mb-3">
                    <img id="detailBlogImage" class="blog-detail-image d-none mb-3" src="" alt="Imagen principal">
                    <div class="d-flex align-items-start justify-content-between flex-wrap">
                        <div>
                            <div class="blog-detail-label">Publicacion</div>
                            <h3 id="detailBlogTitle" class="mb-1">-</h3>
                            <div class="text-muted" id="detailBlogSlug">-</div>
                        </div>
                        <div id="detailBlogStatus" class="mt-2 mt-md-0">-</div>
                    </div>
                    <div class="mt-3">
                        <a class="btn btn-outline-success btn-sm d-none" id="detailPublicLink" href="#" target="_blank" rel="noopener">
                            <i class="fas fa-external-link-alt mr-1"></i> Ver publicacion publica
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="blog-section-title"><i class="fas fa-info-circle"></i> Datos</div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="blog-detail-label">Autor</div>
                                <div id="detailBlogAuthor">-</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="blog-detail-label">Publicado</div>
                                <div id="detailBlogPublishedAt">-</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="blog-detail-label">Registro</div>
                                <div id="detailBlogCreatedAt">-</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="blog-detail-label">Ultima actualizacion</div>
                                <div id="detailBlogUpdatedAt">-</div>
                            </div>
                            <div class="col-12">
                                <div class="blog-detail-label">Resumen</div>
                                <div id="detailBlogSummary">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="blog-section-title"><i class="fas fa-align-left"></i> Contenido</div>
                        <div class="blog-content-preview" id="detailBlogContent"></div>
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
