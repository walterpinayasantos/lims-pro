<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="header-title text-primary"><i class="ti ti-microscope me-1"></i> Validación Técnica</h4>
                        <p class="text-muted font-13 mb-0">
                            Pacientes con muestras recolectadas esperando análisis.
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6 shadow-sm">
                            <?= count($worklist) ?> Pendientes
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Orden</th>
                                <th>Paciente</th>
                                <th>Fecha Ingreso</th>
                                <th class="text-center">Muestras</th>
                                <th class="text-center">Prioridad</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($worklist)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <img src="<?= BASE_URL ?>assets/images/svg/file-searching.svg" height="80" class="mb-3 opacity-50">
                                            <h5 class="mt-2">Todo limpio por ahora</h5>
                                            <p>No hay muestras pendientes de procesamiento.</p>
                                            <a href="<?= BASE_URL ?>sample" class="btn btn-sm btn-outline-primary">
                                                Ir a Toma de Muestras
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($worklist as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark fs-5"><?= $row['code'] ?></span>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary fw-bold">
                                                        <?= substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-14 my-0"><?= $row['first_name'] . ' ' . $row['last_name'] ?></h5>
                                                    <span class="text-muted font-12">
                                                        <?= $row['document_id'] ?> | <?= date_diff(date_create($row['birth_date']), date_create('today'))->y ?> años
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                            <small class="text-muted d-block"><?= date('H:i', strtotime($row['created_at'])) ?></small>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 fs-12">
                                                <i class="ti ti-test-pipe me-1"></i>
                                                <?= $row['total_samples'] ?> Recolectadas
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-secondary">Rutina</span>
                                        </td>

                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?= BASE_URL ?>result/worksheet/<?= $row['id'] ?>" class="btn btn-primary btn-sm shadow-sm" title="Ingresar Resultados">
                                                    <i class="ti ti-edit"></i> <span class="d-none d-md-inline">Procesar</span>
                                                </a>

                                                <a href="<?= BASE_URL ?>result/print/<?= $row['id'] ?>" target="_blank" class="btn btn-info btn-sm shadow-sm text-white" title="Imprimir Informe Clínico">
                                                    <i class="ti ti-printer"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>