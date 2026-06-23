<!-- Modal Customer -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-lg overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header align-items-center"
                style="background: linear-gradient(90deg,#ffffff,#f3f6f8); border-bottom:1px solid #e6eaee;">

                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-light mr-3">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Nuevo Cliente</h5>
                        <small class="text-muted">
                            Gestiona tus clientes del sistema
                        </small>
                    </div>
                </div>

                <button type="button" class="close ml-3" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-3" style="background:#f8fbfc;">
                <form id="customerForm">

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <div class="row">

                                <!-- 🔥 TIPO PERSONA -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        TIPO PERSONA *
                                    </label>
                                    <select name="person_type" id="person_type" class="form-control form-control-sm">
                                        <option value="natural">Persona Natural</option>
                                        <option value="juridica">Persona Jurídica</option>
                                    </select>
                                </div>

                                <!-- 🔥 TIPO DOCUMENTO -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        TIPO DOCUMENTO *
                                    </label>
                                    <select name="document_type" id="document_type"
                                        class="form-control form-control-sm">
                                        <option value="">-- Seleccionar --</option>
                                        <option value="DNI">DNI</option>
                                        <option value="CE">CE</option>
                                        <option value="RUC">RUC</option>
                                    </select>
                                </div>

                                <!-- 🔥 NÚMERO DOCUMENTO -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        N° DOCUMENTO *
                                    </label>
                                    <input type="text" name="document_number" id="document_number"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- 🔥 PERSONA NATURAL -->
                                <div id="naturalFields" class="col-12">
                                    <div class="row">

                                        <div class="form-group col-md-6">
                                            <label class="small font-weight-bold text-secondary">
                                                NOMBRES *
                                            </label>
                                            <input type="text" name="first_name" id="first_name"
                                                class="form-control form-control-sm">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="small font-weight-bold text-secondary">
                                                APELLIDOS *
                                            </label>
                                            <input type="text" name="last_name" id="last_name"
                                                class="form-control form-control-sm">
                                        </div>

                                    </div>
                                </div>

                                <!-- 🔥 PERSONA JURÍDICA / RUC -->
                                <div id="businessFields" class="col-12 d-none">
                                    <div class="row">

                                        <div class="form-group col-md-12">
                                            <label class="small font-weight-bold text-secondary">
                                                RAZÓN SOCIAL *
                                            </label>
                                            <input type="text" name="business_name" id="business_name"
                                                class="form-control form-control-sm">
                                        </div>

                                    </div>
                                </div>

                                <!-- TELÉFONO -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        TELÉFONO
                                    </label>
                                    <input type="text" name="phone" id="phone"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- EMAIL -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        EMAIL
                                    </label>
                                    <input type="email" name="email" id="email"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- DIRECCIÓN -->
                                <div class="form-group col-md-4">
                                    <label class="small font-weight-bold text-secondary">
                                        DIRECCIÓN
                                    </label>
                                    <input type="text" name="address" id="address"
                                        class="form-control form-control-sm">
                                </div>

                                <!-- ESTADO -->
                                <div class="form-group col-md-12">
                                    <label class="small d-block">ESTADO</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status"
                                            name="status" value="1" checked>
                                        <label class="custom-control-label" for="status">
                                            Activo
                                        </label>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="text-right mt-3">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Guardar Cliente
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
