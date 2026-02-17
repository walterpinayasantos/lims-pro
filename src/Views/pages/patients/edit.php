<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Editar Paciente: <?= $patient['first_name'] ?> <?= $patient['last_name'] ?></h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form id="pat_form_edit" autocomplete="off">
                    <input type="hidden" name="id" value="<?= $patient['id'] ?>">

                    <h5 class="mb-3 text-uppercase bg-light p-2"><i class="ti ti-user-circle"></i> Datos Personales</h5>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Documento / CI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="document_id" value="<?= $patient['document_id'] ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Fecha Nacimiento <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="birth_date" value="<?= $patient['birth_date'] ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Sexo <span class="text-danger">*</span></label>
                            <select class="form-select" name="gender" required>
                                <option value="F" <?= $patient['gender'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                                <option value="M" <?= $patient['gender'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="first_name" value="<?= $patient['first_name'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="last_name" value="<?= $patient['last_name'] ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?= $patient['email'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="<?= $patient['phone'] ?>">
                        </div>
                    </div>

                    <h5 class="mb-3 text-uppercase bg-light p-2 mt-4"><i class="ti ti-receipt"></i> Facturación</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIT / CI Factura</label>
                            <input type="text" class="form-control" name="nit_ci_invoice" value="<?= $patient['nit_ci_invoice'] ?>">
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Razón Social</label>
                            <input type="text" class="form-control" name="invoice_name" value="<?= $patient['invoice_name'] ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= BASE_URL ?>patient" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-success" id="pat_btn_update">
                            <i class="ti ti-device-floppy"></i> Actualizar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>