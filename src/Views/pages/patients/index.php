<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Directorio de Pacientes</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <a href="<?= BASE_URL ?>patient/create" class="btn btn-danger rounded-pill mb-3" id="pat_btn_new">
                            <i class="ti ti-plus"></i> Nuevo Paciente
                        </a>
                    </div>
                    <div class="col-sm-8">
                        <div class="text-sm-end">
                            <div class="btn-group mb-3" role="group">
                                <input type="radio" class="btn-check" name="filter_status" id="status_active" value="active" checked>
                                <label class="btn btn-outline-primary" for="status_active"><i class="ti ti-users"></i> Activos</label>

                                <input type="radio" class="btn-check" name="filter_status" id="status_trash" value="trash">
                                <label class="btn btn-outline-secondary" for="status_trash"><i class="ti ti-trash"></i> Papelera</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped dt-responsive nowrap w-100" id="pat_table_list">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Paciente</th>
                                <th id="th_age_info">Edad / Sexo</th>
                                <th>Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>