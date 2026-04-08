<!-- Modal elegante para Post -->
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-file-alt text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="postModalLabel">Nuevo Post</h5>
                        <small class="text-muted">Crea y publica contenido para tu blog</small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="postForm" autocomplete="off" enctype="multipart/form-data" class="row">
                    @csrf

                    <!-- LEFT -->
                    <div class="col-lg-4 mb-3">
                        <div class="card border-0 rounded-lg shadow-sm h-100">
                            <div class="card-body">

                                <small class="text-muted d-block mb-1">Vista previa</small>
                                <img id="postPreviewSide"
                                    src="https://static.vecteezy.com/system/resources/previews/005/951/722/non_2x/preview-interface-icon-illustration-vector.jpg"
                                    class="img-fluid rounded shadow-sm mb-3"
                                    style="object-fit:cover; width:100%;">

                                <div class="text-center mt-2" id="postMetaInfo" style="display:none;">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-user mr-1"></i>
                                        Creado por <strong id="postAuthor">—</strong>
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="far fa-clock mr-1"></i>
                                        <span id="postCreatedAt">—</span>
                                    </small>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-lg shadow-sm">
                            <div class="card-body">

                                <!-- Título + slug -->
                                <div class="form-row">
                                    <div class="form-group col-md-8">
                                        <label class="small font-weight-bold text-secondary">
                                            TÍTULO <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="title" id="title"
                                            class="form-control form-control-sm"
                                            placeholder="Ej: Cómo mejorar tus ventas online">
                                        <span class="invalid-feedback" id="title-error"></span>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label class="small font-weight-bold text-secondary">
                                            SLUG <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="slug" id="slug"
                                            class="form-control form-control-sm"
                                            placeholder="como-mejorar-tus-ventas" readonly>
                                        <span class="invalid-feedback" id="slug-error"></span>
                                    </div>
                                </div>

                                <!-- CATEGORÍA + ESTADO -->
                                <div class="form-row">

                                    <!-- Categoría -->
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

                                    <!-- Estado -->
                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary d-block">
                                            ESTADO DEL POST
                                        </label>
                                        <div class="custom-control custom-switch mt-2">
                                            <input type="checkbox" class="custom-control-input"
                                                id="status" name="status" value="published">
                                            <label class="custom-control-label" for="status">
                                                Publicado
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                <!-- Imagen -->
                                <div class="border rounded-lg p-3 text-center bg-white shadow-sm"
                                    style="border:2px dashed #dee2e6; cursor:pointer;"
                                    onclick="document.getElementById('image').click()">

                                    <input type="file" name="image" id="image" accept="image/*" hidden>

                                    <div id="imageUploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <div class="font-weight-600 text-secondary">
                                            Arrastra una imagen aquí
                                        </div>
                                        <small class="text-muted">
                                            o haz clic para seleccionar
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- CONTENIDO -->
                    <div class="col-lg-12 mt-3">
                        <div class="card border-0 rounded-lg shadow-sm">
                            <div class="card-body">

                                <div class="form-group">
                                    <label class="small font-weight-bold text-secondary">
                                        CONTENIDO <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="content" id="content" rows="6"
                                        class="form-control form-control-sm"
                                        placeholder="Escribe el contenido del post..."></textarea>
                                    <span class="invalid-feedback" id="content-error"></span>
                                </div>

                                <div class="form-row mt-4">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-light border mr-2"
                                            data-dismiss="modal">
                                            <i class="fas fa-times mr-1"></i> Cancelar
                                        </button>

                                        <button type="submit" id="btnSavePost" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Guardar Post
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
