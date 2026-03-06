<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="header-title text-primary">
                            <i class="ti ti-signature me-1"></i> Firma Facultativa
                        </h4>
                        <p class="text-muted font-13 mb-0">
                            Órdenes con resultados completos esperando validación médica profesional.
                        </p>
                    </div>
                    <span class="badge bg-primary rounded-pill p-2">
                        <?= count($orders) ?> Pendientes
                    </span>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="text-center p-5">
                        <img src="<?= BASE_URL ?>assets/images/empty-list.png" alt="Vacío" height="80" class="mb-3 opacity-50">
                        <h4 class="text-muted">Todo limpio por ahora</h4>
                        <p class="text-muted">No hay muestras pendientes de firma facultativa.</p>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>result" class="btn btn-outline-primary">
                                Ir a Resultados y Validación
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Orden</th>
                                    <th>Paciente</th>
                                    <th>Fecha Ingreso</th>
                                    <th>Muestras</th>
                                    <th>Prioridad</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong><?= $order['code'] ?></strong></td>
                                        <td><?= $order['first_name'] . ' ' . $order['last_name'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-soft-info text-info">
                                                <?= $order['total_items'] ?? '1' ?> Items
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-success text-success">Normal</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= BASE_URL ?>result/review/<?= $order['id'] ?>"
                                                class="btn btn-sm btn-primary btn-rounded">
                                                Revisar y Firmar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>