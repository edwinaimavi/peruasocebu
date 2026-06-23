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

                                        <!-- CATEGORÍA -->
                                        <div class="form-group col-md-4">
                                            <label class="small">CATEGORÍA</label>
                                            <select name="category_id" id="category_id" class="form-control">
                                                <option value="">Seleccione</option>

                                                @foreach ($categories as $category)
                                                    @include('admin.categories.partials.category-option', [
                                                        'category' => $category,
                                                        'level' => 0,
                                                    ])
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- MARCA 🔥 -->
                                        <div class="form-group col-md-4">
                                            <label class="small">MARCA</label>
                                            <select name="brand_id" id="brand_id" class="form-control">
                                                <option value="">Seleccione</option>

                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label class="small">MODELO</label>
                                            <input type="text" name="model" id="model"
                                                class="form-control form-control-sm">
                                        </div>

                                        <!-- TIPO -->


                                    </div>
                                    <!-- Precio + Estado -->
                                    <div class="form-row">

                                        <div class="form-group col-md-4">
                                            <label class="small">TIPO *</label>
                                            <select name="type" id="type" class="form-control form-control-sm">
                                                <option value="">Seleccione</option>
                                                <option value="sistema">Sistema</option>
                                                <option value="servicio">Servicio</option>
                                            </select>
                                        </div>
                                        <!-- ========================= -->
                                        <!-- 💰 PRECIOS DINÁMICOS -->
                                        <!-- ========================= -->
                                        <div class="col-md-12">

                                            <div class="border rounded p-2 bg-light">

                                                <label class="small font-weight-bold text-secondary mb-2 d-block">
                                                    💰 PRECIOS POR TIPO
                                                </label>

                                                <div class="row">

                                                    @foreach ($priceTypes as $type)
                                                        <div class="form-group col-md-4">

                                                            <label class="small">
                                                                {{ $type->name }}
                                                            </label>

                                                            <input type="number" step="0.01"
                                                                class="form-control form-control-sm price-input"
                                                                name="prices[{{ $type->id }}]"
                                                                data-type="{{ $type->id }}" placeholder="0.00">

                                                        </div>
                                                    @endforeach

                                                </div>

                                                <small class="text-muted">
                                                    Define precios para cada tipo de cliente
                                                </small>

                                            </div>

                                        </div>

                                        <div class="form-group col-md-4">
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

                            {{--   <div class="form-group">
                                <label class="small">DESCRIPCIÓN *</label>
                                <textarea name="description" id="description" class="form-control form-control-sm"></textarea>
                            </div> --}}
                            <div class="form-group">
                                <label class="small font-weight-bold text-secondary">
                                    DESCRIPCIÓN <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" rows="6" class="form-control form-control-sm"
                                    placeholder="Escribe el contenido del post..."></textarea>
                                <span class="invalid-feedback" id="content-error"></span>
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
