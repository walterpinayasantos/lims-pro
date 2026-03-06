<div class="row" id="worksheet_container" data-order-id="<?= $order['id'] ?>">
    <div class="col-12">
        <div class="card bg-light border-primary border-top border-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mt-0 header-title text-primary"><?= $order['first_name'] . ' ' . $order['last_name'] ?></h4>
                        <p class="text-muted mb-0">
                            Edad: <?= date_diff(date_create($order['birth_date']), date_create('today'))->y ?> años |
                            Sexo: <?= $order['gender'] ?> |
                            Orden: <strong><?= $order['code'] ?></strong>
                        </p>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>result" class="btn btn-outline-secondary me-2">Cancelar</a>
                        <button id="btn_save_results" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i> Validar y Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="results_table">
                        <thead class="table-dark">
                            <tr>
                                <th>Examen / Parámetro</th>
                                <th style="width: 200px;">Resultado</th>
                                <th>Unidad</th>
                                <th>Valores de Referencia</th>
                                <th>Observaciones Técnicas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr class="result-row"
                                    data-item-id="<?= $item['id'] ?>"
                                    data-low="<?= $item['dec_ref_low'] ?? '' ?>"
                                    data-high="<?= $item['dec_ref_high'] ?? '' ?>"
                                    data-crit-low="<?= $item['crit_ref_low'] ?? '' ?>"
                                    data-crit-high="<?= $item['crit_ref_high'] ?? '' ?>">

                                    <td class="fw-bold">
                                        <?= $item['test_name'] ?>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input type="number"
                                                step="any"
                                                class="form-control result-input"
                                                value="<?= $item['current_result'] ?? '' ?>"
                                                placeholder="Ingrese valor">
                                            <span class="input-group-text flag-badge" style="display:none;"></span>
                                        </div>
                                    </td>

                                    <td class="text-muted"><?= $item['unit'] ?? 'u' ?></td>

                                    <td>
                                        <?php if (($item['dec_ref_low'] ?? null) !== null): ?>
                                            <small class="d-block text-muted">
                                                Min: <?= $item['dec_ref_low'] ?> - Max: <?= $item['dec_ref_high'] ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <input type="text" class="form-control form-control-sm result-comment"
                                            placeholder="Opcional..." value="<?= $item['comments'] ?? '' ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>