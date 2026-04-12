id="previewContainer" class="row"></div>
<!-- Modal elegante para Product -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="productModalLabel"
    aria-hidden="true">

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
                <form id="productForm" enctype="multipart/form-data">

                    @csrf

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <!-- FILA PRINCIPAL -->
                            <div class="row">

                                <!-- LEFT (FORMULARIO) -->
                                <div class="col-lg-8">

                                    <!-- Nombre + slug -->
                                    <div class="form-row">
                                        <div class="form-group col-md-8">
                                            <label class="small font-weight-bold text-secondary">
                                                NOMBRE *
                                            </label>
                                            <input type="text" name="name" id="name"
                                                class="form-control form-control-sm">
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="small font-weight-bold text-secondary">
                                                SLUG *
                                            </label>
                                            <input type="text" name="slug" id="slug"
                                                class="form-control form-control-sm" readonly>
                                        </div>
                                    </div>

                                    <!-- Categoría + Tipo -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="small">CATEGORÍA</label>
                                            <select name="category_id" id="category_id"
                                                class="form-control form-control-sm">
                                                <option value="">Seleccione</option>
                                                @foreach ($categories as $category)
                                                    @if ($category->status)
                                                        <option value="{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="small">TIPO *</label>
                                            <select name="type" id="type" class="form-control form-control-sm">
                                                <option value="">Seleccione</option>
                                                <option value="sistema">Sistema</option>
                                                <option value="servicio">Servicio</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Precio + Estado -->
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label class="small">PRECIO</label>
                                            <input type="number" name="price" id="price"
                                                class="form-control form-control-sm">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="small d-block">ESTADO</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="status"
                                                    name="status" value="published">
                                                <label class="custom-control-label" for="status">
                                                    Publicado
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- RIGHT (GALERÍA) -->
                                <div class="col-lg-4">

                                    <div class="gallery-card">

                                        <!-- HEADER -->
                                        <div class="gallery-header">
                                            <span>Galería</span>
                                            <small>Sube múltiples imágenes</small>
                                        </div>

                                        <!-- INPUT -->
                                        <label class="upload-box">
                                            <input type="file" id="images" multiple accept="image/*" hidden>

                                            <div class="upload-content">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Haz clic o arrastra imágenes</span>
                                            </div>
                                        </label>

                                        <!-- GRID -->
                                        <div id="previewContainer" class="gallery-grid"></div>

                                        <!-- EMPTY STATE -->
                                        <div id="emptyGallery" class="empty-gallery">
                                            <i class="far fa-images"></i>
                                            <p>No hay imágenes aún</p>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">

                            <div class="form-group">
                                <label class="small">DESCRIPCIÓN CORTA</label>
                                <input type="text" name="short_description" id="short_description"
                                    class="form-control form-control-sm">
                            </div>

                            <div class="form-group">
                                <label class="small">DESCRIPCIÓN *</label>
                                <textarea name="description" id="description" class="form-control form-control-sm"></textarea>
                            </div>

                            <div class="text-right">
                                <button type="button" class="btn btn-light" data-dismiss="modal">
                                    Cancelar
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Guardar Producto
                                </button>
                            </div>

                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
