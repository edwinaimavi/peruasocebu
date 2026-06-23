<div class="modal fade" id="productViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow border-0">

            <!-- HEADER -->
            <div class="modal-header bg-white border-bottom">
                <div>
                    <h5 class="modal-title mb-0 font-weight-bold text-dark">
                        <i class="fas fa-eye text-info mr-2"></i>
                        Detalle del Producto
                    </h5>
                    <small class="text-muted">Vista previa del producto / servicio</small>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <!-- NAME -->
                <h4 id="viewProductName" class="font-weight-bold mb-2 text-dark"></h4>

                <!-- META INFO -->
                <div class="mb-3 text-muted small">

                    <div class="mb-3">
                        <h6 class="font-weight-bold text-dark mb-2">
                            <i class="fas fa-money-bill-wave text-success mr-1"></i>
                            Precios
                        </h6>

                        <div id="viewProductPrices" class="border rounded p-2 bg-light"></div>
                    </div>

                    <span class="mr-3">
                        <i class="fas fa-barcode mr-1"></i>
                        Modelo: <span id="viewProductModel"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-link mr-1"></i>
                        <span id="viewProductSlug"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-folder-open mr-1"></i>
                        <span id="viewProductCategory"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-industry mr-1"></i>
                        Marca: <span id="viewProductBrand"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-tag mr-1"></i>
                        <span id="viewProductPrice"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-cubes mr-1"></i>
                        <span id="viewProductType"></span>
                    </span>

                    <span class="mr-3">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span id="viewProductStatus"></span>
                    </span>

                    <span>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <span id="viewProductCreatedAt"></span>
                    </span>

                </div>

                <!-- GALERÍA -->
                <div class="mb-4">

                    <div class="text-center mb-3">
                        <img id="mainImage" class="img-fluid rounded shadow"
                            style="max-height: 350px; object-fit: cover;">
                    </div>

                    <div id="imageThumbnails" class="d-flex flex-wrap gap-2 justify-content-center"></div>

                </div>

                <!-- SHORT -->
                <div id="viewProductShort" class="border rounded p-2 bg-light mb-3 text-muted"></div>

                <!-- DESCRIPTION -->
                <div id="viewProductDescription" class="border rounded p-3 bg-light"></div>

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
