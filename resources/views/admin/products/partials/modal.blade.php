<!-- Modal elegante para Product -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog"
    aria-labelledby="productModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-box text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="productModalLabel">Nuevo Producto</h5>
                        <small class="text-muted">
                            Registra sistemas o servicios vendibles
                        </small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="productForm" autocomplete="off" enctype="multipart/form-data" class="row">
                    @csrf

                    <!-- LEFT -->
                    <div class="col-lg-4 mb-3">
                        <div class="card border-0 rounded-lg shadow-sm h-100">
                            <div class="card-body">

                                <small class="text-muted d-block mb-1">Vista previa</small>

                                <img id="productPreviewSide"
                                    src="https://static.vecteezy.com/system/resources/previews/005/951/722/non_2x/preview-interface-icon-illustration-vector.jpg"
                                    class="img-fluid rounded shadow-sm mb-3"
                                    style="object-fit:cover;width:100%;">

                                <div class="text-center mt-2" id="productMetaInfo" style="display:none;">
                                    <small class="text-muted d-block">
                                        <i class="far fa-clock mr-1"></i>
                                        <span id="productCreatedAt">—</span>
                                    </small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-lg shadow-sm">
                            <div class="card-body">

                                <!-- Nombre + slug -->
                                <div class="form-row">
                                    <div class="form-group col-md-8">
                                        <label class="small font-weight-bold text-secondary">
                                            NOMBRE <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control form-control-sm"
                                            placeholder="Ej: Sistema de Ventas POS">
                                        <span class="invalid-feedback" id="name-error"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="small font-weight-bold text-secondary">
                                            SLUG <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="slug" id="slug"
                                            class="form-control form-control-sm"
                                            placeholder="sistema-ventas-pos" readonly>
                                        <span class="invalid-feedback" id="slug-error"></span>
                                    </div>
                                </div>

                                <!-- Categoría + Tipo -->
                                <div class="form-row">

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            CATEGORÍA
                                        </label>
                                        <select name="category_id" id="category_id"
                                            class="form-control form-control-sm">
                                            <option value="">Seleccione una categoría</option>
                                            @foreach ($categories as $category)
                                                @if ($category->status)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="category_id-error"></span>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            TIPO <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="type"
                                            class="form-control form-control-sm">
                                            <option value="">Seleccione</option>
                                            <option value="sistema">Sistema</option>
                                            <option value="servicio">Servicio</option>
                                        </select>
                                        <span class="invalid-feedback" id="type-error"></span>
                                    </div>

                                </div>

                                <!-- Precio + Estado -->
                                <div class="form-row">

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            PRECIO (S/)
                                        </label>
                                        <input type="number" step="0.01" name="price" id="price"
                                            class="form-control form-control-sm"
                                            placeholder="0.00">
                                        <span class="invalid-feedback" id="price-error"></span>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary d-block">
                                            ESTADO
                                        </label>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox"
                                                class="custom-control-input"
                                                id="status"
                                                name="status"
                                                value="published">
                                            <label class="custom-control-label" for="status">
                                                Publicado
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <!-- Imagen -->
                               <!-- GALERÍA DE IMÁGENES -->
<div class="border rounded-lg p-3 bg-white shadow-sm">

    <label class="small font-weight-bold text-secondary d-block mb-2">
        GALERÍA DE IMÁGENES
    </label>

    <input type="file"
        name="images[]"
        id="images"
        multiple
        accept="image/*"
        class="form-control form-control-sm mb-3">

    <!-- PREVIEW GRID -->
    <div id="previewContainer" class="row"></div>

</div>

                            </div>
                        </div>
                    </div>

                    <!-- DESCRIPCIONES -->
                    <div class="col-lg-12 mt-3">
                        <div class="card border-0 rounded-lg shadow-sm">
                            <div class="card-body">

                                <div class="form-group">
                                    <label class="small font-weight-bold text-secondary">
                                        DESCRIPCIÓN CORTA
                                    </label>
                                    <input type="text" name="short_description"
                                        id="short_description"
                                        class="form-control form-control-sm"
                                        placeholder="Resumen breve del producto">
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold text-secondary">
                                        DESCRIPCIÓN <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description" id="description"
                                        rows="5"
                                        class="form-control form-control-sm"
                                        placeholder="Describe el producto o servicio..."></textarea>
                                    <span class="invalid-feedback" id="description-error"></span>
                                </div>

                                <div class="form-row mt-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button"
                                            class="btn btn-light border mr-2"
                                            data-dismiss="modal">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </button>

                                        <button type="submit"
                                            id="btnSaveProduct"
                                            class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Guardar Producto
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
