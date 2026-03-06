<div id="review_container" data-order-id="<?= $order['id'] ?>">
    <table class="table">
        <tbody>
            <?php foreach ($results as $res): ?>
                <tr class="review-row" data-item-id="<?= $res['item_id'] ?>">
                    <td><?= $res['test_name'] ?></td>
                    <td>
                        <input type="text" class="form-control result-input" value="<?= $res['result_value'] ?>">
                    </td>
                    <td><?= $res['unit'] ?></td>
                    <td>
                        <input type="text" class="form-control result-comment" value="<?= $res['comments'] ?>" placeholder="Nota...">
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button id="btn_approve_signature" class="btn btn-success w-100">
        <i class="ti ti-signature"></i> CONFIRMAR Y FIRMAR INFORME
    </button>
</div>
<div class="card border-primary">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <span><i class="ti ti-file-certificate me-2"></i>REVISIÓN DE INFORME: <?= $order['code'] ?></span>
        <span class="badge bg-white text-primary">Estado: Pendiente de Firma</span>
    </div>
    <div class="card-body">
        <table class="table table-sm">
            <thead>
                <tr class="table-light">
                    <th>Análisis</th>
                    <th width="150">Resultado</th>
                    <th>Referencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $res): ?>
                    <tr>
                        <td><?= $res['test_name'] ?></td>
                        <td>
                            <input type="text" class="form-control form-control-sm fw-bold border-0 bg-light"
                                value="<?= $res['result_value'] ?>">
                        </td>
                        <td class="small text-muted"><?= $res['dec_ref_low'] ?> - <?= $res['dec_ref_high'] ?> <?= $res['unit'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-end">
        <button class="btn btn-warning me-2" onclick="history.back()">Corregir en Hoja</button>
        <button id="btn_approve_signature" class="btn btn-success">
            <i class="ti ti-edit"></i> APROBAR Y FIRMAR INFORME
        </button>
    </div>
</div>