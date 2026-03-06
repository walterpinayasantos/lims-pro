<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white"><i class="ti ti-clock me-1"></i> Sala de Espera</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="waiting_list">
                    <?php if (empty($pendingOrders)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="ti ti-check-double fs-1"></i>
                            <p class="mt-2">No hay pacientes pendientes.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pendingOrders as $order): ?>
                            <a href="javascript:void(0);" onclick="loadPatientSamples(<?= $order['id'] ?>, '<?= $order['first_name'] ?> <?= $order['last_name'] ?>', '<?= $order['code'] ?>')"
                                class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary"><?= $order['first_name'] . ' ' . $order['last_name'] ?></h5>
                                    <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </div>
                                <p class="mb-1 small">Orden: <strong><?= $order['code'] ?></strong></p>
                                <small>CI: <?= $order['document_id'] ?></small>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card" id="work_area" style="display:none;">
            <div class="card-header border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="header-title mb-0" id="selected_patient_name">Seleccione Paciente</h4>
                        <small class="text-muted" id="selected_order_code">---</small>
                    </div>
                    <button class="btn btn-success" id="btn_confirm_collection">
                        <i class="ti ti-syringe me-1"></i> Confirmar Recolección
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning border-0 shadow-sm mb-3">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-alert-triangle fs-2 me-2"></i>
                        <div>
                            <strong>Protocolo de Identificación:</strong><br>
                            Pregunte al paciente: <em>"¿Cuál es su nombre completo y fecha de nacimiento?"</em>
                        </div>
                    </div>
                </div>

                <h5 class="mt-3">Tubos e Insumos Requeridos</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-centered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20px;">
                                    <input type="checkbox" class="form-check-input" id="check_all" checked>
                                </th>
                                <th>Contenedor</th>
                                <th>Vol</th>
                                <th>Examen / Muestra</th>
                                <th>Instrucciones</th>
                            </tr>
                        </thead>
                        <tbody id="samples_list_body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card text-center py-5" id="empty_work_area">
            <div class="card-body">
                <div class="avatar-lg rounded-circle bg-light text-primary mx-auto mb-3">
                    <i class="ti ti-virus-search fs-1 align-middle pt-2 d-inline-block"></i>
                </div>
                <h4>Área de Flebotomía</h4>
                <p class="text-muted">Seleccione un paciente para ver los requisitos de toma de muestra.</p>
            </div>
        </div>
    </div>
</div>