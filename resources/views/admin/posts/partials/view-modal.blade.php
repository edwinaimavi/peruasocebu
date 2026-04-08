<div class="modal fade" id="postViewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content shadow border-0">

            <!-- HEADER -->
            <div class="modal-header bg-white border-bottom">
                <div>
                    <h5 class="modal-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-eye text-info mr-2"></i>
                        Detalle del Post
                    </h5>
                    <small class="text-muted">Vista previa del contenido</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- TITLE -->
                <h4 id="viewTitle" class="font-weight-bold mb-2 text-dark"></h4>

                <!-- META INFO -->
                <div class="mb-3 text-muted small">
                    <span class="mr-3">
                        <i class="fas fa-link mr-1"></i>
                        <span id="viewSlug"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-folder-open mr-1"></i>
                        <span id="viewCategory"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-user mr-1"></i>
                        <span id="viewAuthor"></span>
                    </span>

                    <span>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <span id="viewCreatedAt"></span>
                    </span>
                </div>

                <!-- IMAGE -->
                <div class="text-center mb-4">
                    <img id="viewImage" class="img-fluid rounded shadow-sm d-none"
                        style="max-height: 350px; object-fit: cover;">
                </div>

                <!-- CONTENT -->
                <div id="viewContent" class="border rounded p-3 bg-light" style="line-height: 1.7;">
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>
