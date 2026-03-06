<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="header-title">Revisión de Resultados</h4>
                        <p class="text-muted mb-0">Orden: <strong><?= $order['code'] ?></strong></p>
                    </div>
                    <div>
                        <h3><?= $order['first_name'] . ' ' . $order['last_name'] ?></h3>
                        <span class="badge bg-light text-dark border"><?= $order['document_id'] ?></span>
                        <span class="badge bg-light text-dark border"><?= $order['gender'] ?></span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Examen</th>
                                <th class="text-center">Resultado</th>
                                <th>Unidad</th>
                                <th>Referencia</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentArea = '';
                            foreach ($results as $res):
                                // Agrupar por Área (Hematología, Química, etc.)
                                if ($currentArea != $res['area_name']):
                                    $currentArea = $res['area_name'];
                                    echo "<tr><td colspan='5' class='bg-soft-primary fw-bold text-primary mt-2'>$currentArea</td></tr>";
                                endif;
                            ?>
                                <tr>
                                    <td><?= $res['test_name'] ?></td>

                                    <td class="text-center fw-bold fs-15 
                                    <?= ($res['flag'] == 'HIGH' || $res['flag'] == 'LOW') ? 'text-danger' : 'text-dark' ?>">
                                        <?= $res['result_value'] ?>
                                    </td>

                                    <td><small><?= $res['unit'] ?></small></td>
                                    <td><small class="text-muted"><?= $res['dec_ref_low'] ?> - <?= $res['dec_ref_high'] ?></small></td>

                                    <td>
                                        <?php if ($res['flag'] != 'NORMAL'): ?>
                                            <span class="badge badge-outline-danger"><?= $res['flag'] ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-outline-success">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($res['comments'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-muted fst-italic small pl-4">
                                            <i class="ti ti-info-circle"></i> Nota: <?= $res['comments'] ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>result/signature" class="btn btn-light">Cancelar / Volver</a>

                    <button onclick="signOrder(<?= $order['id'] ?>)" class="btn btn-success btn-lg shadow-sm">
                        <i class="ti ti-writing-sign me-1"></i> Aprobar y Firmar Digitalmente
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function signOrder(id) {
        if (!confirm('¿Confirma que ha revisado los valores y desea FIRMAR esta orden? \n\nEsta acción habilitará la descarga para el paciente.')) return;

        fetch('<?= BASE_URL ?>result/approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: id
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('¡Orden validada correctamente!');
                    window.location.href = '<?= BASE_URL ?>result/signature'; // Vuelve a la lista
                } else {
                    alert('Error: ' + data.message);
                }
            });
    }
</script>