<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="page-title">Configurar Examen: <?= $test['name'] ?></h4>
            <a href="<?= BASE_URL ?>exams" class="btn btn-secondary btn-sm">Volver</a>
        </div>
    </div>
</div>

<form action="<?= BASE_URL ?>exams/update" method="POST" id="form-edit-exam">
    <input type="hidden" name="id" value="<?= $test['id'] ?>">

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Información General e Inteligencia</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Examen</label>
                            <input type="text" class="form-control" name="name" value="<?= $test['name'] ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Abreviación</label>
                            <input type="text" class="form-control" name="abbreviation" value="<?= $test['abbreviation'] ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <div class="input-group">
                                <input type="text" class="form-control symbol-input" name="unit" id="test_unit" value="<?= $test['unit'] ?>">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-math-symbols"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <h5 class="header-title mt-4 mb-3">Valores de Referencia Registrados</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Género</th>
                                    <th>Edad (Días)</th>
                                    <th>Población</th>
                                    <th>Min - Max</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranges as $range): ?>
                                    <tr>
                                        <td><?= $range['gender'] ?></td>
                                        <td><?= $range['age_min_days'] ?> - <?= $range['age_max_days'] ?></td>
                                        <td><?= $range['population_type'] ?></td>
                                        <td><strong><?= $range['range_min'] ?> - <?= $range['range_max'] ?></strong></td>
                                        <td><button type="button" class="btn btn-xs btn-outline-danger"><i class="ti ti-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="header-title mb-3">Logística y Bioseguridad</h5>

                    <div class="mb-3">
                        <label class="form-label text-info"><i class="ti ti-test-pipe"></i> Tipo de Contenedor</label>
                        <input type="text" class="form-control" name="container_type" placeholder="Ej: Tubo Tapa Lila">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-warning"><i class="ti ti-biohazard"></i> Nivel Bioseguridad</label>
                        <select class="form-select" name="biosafety_level">
                            <option value="BSL-1">BSL-1 (Bajo)</option>
                            <option value="BSL-2">BSL-2 (Moderado)</option>
                            <option value="BSL-3">BSL-3 (Alto Riesgo)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="ti ti-clock"></i> Estabilidad Temp. Amb.</label>
                        <input type="text" class="form-control" name="stability_room_temp" placeholder="Ej: 4 horas">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Instrucciones de Cuidado Post-Toma</label>
                        <textarea class="form-control" name="aftercare_instructions" rows="2" placeholder="Basado en Chernecky..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i> Guardar Cambios</button>
        </div>
    </div>
</form>