<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="header-title">Órdenes de Laboratorio</h4>
                        <p class="text-muted font-13 mb-0">
                            Mostrando las últimas 100 solicitudes ingresadas.
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="<?= BASE_URL ?>order/create" class="btn btn-primary shadow-sm">
                            <i class="ti ti-plus me-1"></i> Nueva Orden
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-centered table-nowrap table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Folio / Código</th>
                                <th>Paciente</th>
                                <th>Fecha Ingreso</th>
                                <th class="text-end">Total (Bs)</th>
                                <th class="text-end">Saldo (Bs)</th>
                                <th class="text-center">Estado Pago</th>
                                <th class="text-center">Estado Lab</th>
                                <th class="text-center" style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-clipboard-x fs-1"></i><br>
                                            No hay órdenes registradas aún.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="fw-bold text-primary">
                                            <a href="<?= BASE_URL ?>order/print/<?= $order['id'] ?>" class="text-reset">
                                                <?= $order['code'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title rounded-circle bg-light text-primary fw-bold">
                                                        <?= strtoupper(substr($order['first_name'], 0, 1) . substr($order['last_name'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <h5 class="font-14 my-0"><?= $order['first_name'] . ' ' . $order['last_name'] ?></h5>
                                                    <span class="text-muted font-12"><?= $order['document_id'] ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($order['created_at'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                        </td>
                                        <td class="text-end fw-bold">
                                            <?= number_format((float)$order['total_amount'], 2) ?>
                                        </td>

                                        <td class="text-end">
                                            <?php if ($order['balance_due'] > 0): ?>
                                                <span class="text-danger fw-bold"><?= number_format((float)$order['balance_due'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="text-success">-</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-center">
                                            <?php
                                            $badgeClass = 'bg-secondary';
                                            $badgeText = 'Desconocido';

                                            switch ($order['payment_status']) {
                                                case 'PAID':
                                                    $badgeClass = 'bg-success';
                                                    $badgeText = 'PAGADO';
                                                    break;
                                                case 'PARTIAL':
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $badgeText = 'PARCIAL';
                                                    break;
                                                case 'UNPAID':
                                                    $badgeClass = 'bg-danger';
                                                    $badgeText = 'PENDIENTE';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?> p-1 px-2"><?= $badgeText ?></span>
                                        </td>

                                        <td class="text-center">
                                            <span class="badge bg-soft-info text-info border border-info">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>

                                        <td class="text-center">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots-vertical fs-5"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="<?= BASE_URL ?>order/print/<?= $order['id'] ?>" class="dropdown-item">
                                                        <i class="ti ti-printer me-1"></i> Imprimir Recibo
                                                    </a>

                                                    <?php if ($order['balance_due'] > 0): ?>
                                                        <a href="javascript:void(0);" onclick="alert('Módulo de Caja Próximamente')" class="dropdown-item text-success">
                                                            <i class="ti ti-cash me-1"></i> Cobrar Saldo
                                                        </a>
                                                    <?php endif; ?>

                                                    <div class="dropdown-divider"></div>

                                                    <a href="javascript:void(0);" class="dropdown-item text-danger">
                                                        <i class="ti ti-trash me-1"></i> Anular Orden
                                                    </a>
                                                </div>
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