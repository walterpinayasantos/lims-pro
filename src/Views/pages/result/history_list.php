<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="header-title mb-0">Historial de Resultados Entregados</h4>
        </div>

        <form method="GET" action="<?= BASE_URL ?>result/history" class="row g-3 mb-4 bg-light p-3 rounded border">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Fecha Inicio:</label>
                <input type="date" name="start" class="form-control" value="<?= $filters['start'] ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Fecha Fin:</label>
                <input type="date" name="end" class="form-control" value="<?= $filters['end'] ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="ti ti-filter"></i> Filtrar
                </button>
            </div>
            <div class="col-md-4 d-flex align-items-end justify-content-end text-muted small">
                Mostrando registros del <?= date('d/m/Y', strtotime($filters['start'])) ?> al <?= date('d/m/Y', strtotime($filters['end'])) ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Orden</th>
                        <th>Paciente</th>
                        <th>Fecha Entrega</th>
                        <th>Recogió</th>
                        <th>Entregado Por</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                                No se encontraron entregas en el rango de fechas seleccionado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= $order['code'] ?></strong></td>
                                <td>
                                    <?= $order['first_name'] . ' ' . $order['last_name'] ?><br>
                                    <small class="text-muted"><?= $order['document_id'] ?></small>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></td>

                                <td>
                                    <span class="badge bg-soft-success text-success border border-success">
                                        <i class="ti ti-user-check"></i> <?= $order['delivered_to'] ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-light text-dark">ID: <?= $order['delivered_by'] ?? '-' ?></span>
                                </td>

                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>result/print/<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-ghost-primary" title="Reimprimir Copia">
                                        <i class="ti ti-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>