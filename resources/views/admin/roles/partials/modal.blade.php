<!-- Modal elegante para Rol -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3 icon_modal">
                        <i class="fas fa-user-shield text-secondary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="roleModalLabel">Nuevo Rol</h5>
                        <small class="text-muted">Define el rol y asigna permisos</small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="roleForm" autocomplete="off">
                    @csrf

                    <!-- Nombre del Rol -->
                    <div class="card border-0 rounded-lg shadow-sm mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-secondary">
                                    Nombre del Rol <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" name="name"
                                    placeholder="Ej: Administrador, Cajero, Entrenador" required>
                            </div>

                            <div id="error-messages" class="alert alert-danger d-none mt-3"></div>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="card border-0 rounded-lg shadow-sm">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 font-weight-bold text-secondary">
                                <i class="fas fa-key mr-1"></i> Lista de Permisos
                            </h6>
                            <small class="text-muted">
                                Activa los permisos que tendrá este rol
                            </small>
                        </div>

                        <div class="card-body pt-2" style="max-height: 320px; overflow-y: auto;">
                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-6">
                                        <div class="custom-control custom-switch mb-2">
                                            <input type="checkbox" class="custom-control-input"
                                                value="{{ $permission->name }}" id="permission_{{ $permission->id }}"
                                                name="permissions[]">
                                            <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->description }}
                                                <small class="text-muted d-block">
                                                    {{ $permission->guard_name }}
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- ACTIONS -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-light border mr-2" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Guardar Rol
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
