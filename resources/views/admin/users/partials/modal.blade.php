<!-- Modal elegante para Usuario -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-user text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="userModalLabel">Nuevo Usuario</h5>
                        <small class="text-muted">Registro del sistema · campos obligatorios (*)</small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="userForm" enctype="multipart/form-data" autocomplete="off" class="row">
                    @csrf

                    <!-- LEFT: Avatar + meta -->
                    <div class="col-lg-4 mb-3">
                        <div class="card border-0 rounded-lg shadow-sm h-100">
                            <div class="card-body text-center py-4">

                                <div class="avatar-preview mb-3">
                                    <img id="imgPreview"
                                        src="https://www.shutterstock.com/image-vector/default-avatar-profile-icon-social-600nw-1906669723.jpg"
                                        class="rounded-circle img-fluid"
                                        style="width:140px;height:140px;object-fit:cover;border:6px solid #fff;
                              box-shadow:0 6px 18px rgba(47,63,78,0.08);">
                                </div>

                                <label for="image" class="btn btn-sm btn-light border text-secondary mb-2"
                                    style="cursor:pointer;">
                                    <i class="fas fa-camera mr-1"></i> Subir foto
                                </label>
                                <input type="file" id="image" name="image" accept="image/*" class="d-none"
                                    onchange="previewImage(event,'#imgPreview')">

                                <hr>

                                <div class="text-left">
                                    <small class="text-muted">Rol asignado</small>
                                    <div id="left_role" class="font-weight-600">—</div>

                                    <small class="text-muted d-block mt-2">Estado</small>
                                    <div class="badge badge-success py-2 px-3 mt-1">Activo</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: Formulario -->
                    <div class="col-lg-8">
                        <div class="card border-0 rounded-lg shadow-sm">
                            <div class="card-body">

                                <!-- Row 1 -->
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label class="small font-weight-bold text-secondary">
                                            DNI <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-sm" name="dni"
                                            placeholder="00000000" required>
                                    </div>

                                    <div class="form-group col-md-8">
                                        <label class="small font-weight-bold text-secondary">
                                            Nombres <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-sm" name="name"
                                            placeholder="Nombres completos" required>
                                    </div>
                                </div>

                                <!-- Row 2 -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            Apellidos <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-sm" name="lastname"
                                            placeholder="Apellidos" required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control form-control-sm" name="email"
                                            placeholder="correo@dominio.com" required>
                                    </div>
                                </div>

                                <!-- Row 3 -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            Contraseña
                                        </label>
                                        <input type="password" class="form-control form-control-sm" name="password"
                                            placeholder="••••••••">
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            Repetir contraseña
                                        </label>
                                        <input type="password" class="form-control form-control-sm"
                                            name="password_confirmation" placeholder="••••••••">
                                    </div>
                                </div>

                                <!-- Row 4 -->
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label class="small font-weight-bold text-secondary">Celular</label>
                                        <input type="text" class="form-control form-control-sm" name="phone"
                                            placeholder="9XXXXXXXX">
                                    </div>

                                    <div class="form-group col-md-7">
                                        <label class="small font-weight-bold text-secondary">Dirección</label>
                                        <input type="text" class="form-control form-control-sm" name="address"
                                            placeholder="Dirección del usuario">
                                    </div>
                                </div>

                                <!-- Row 5 -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">
                                            Rol <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control form-control-sm" name="role" required>
                                            <option value="">Seleccione un rol</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label class="small font-weight-bold text-secondary">Estado</label>
                                        <select class="form-control form-control-sm" name="status">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="form-row mt-3">
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="button" class="btn btn-light border mr-2"
                                            data-dismiss="modal">
                                            <i class="fas fa-times mr-1"></i> Cerrar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Guardar Usuario
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
