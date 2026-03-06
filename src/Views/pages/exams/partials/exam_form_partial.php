<form id="formEditExam">
    <ul class="nav nav-tabs nav-bordered mb-3">
        <li class="nav-item">
            <a href="#info-general" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                Generales y Rangos
            </a>
        </li>
        <?php if ($exam['is_calculated']): ?>
            <li class="nav-item">
                <a href="#config-formula" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    Configuración de Fórmula
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content">
        <div class="tab-pane show active" id="info-general">
        </div>

        <?php if ($exam['is_calculated']): ?>
            <div class="tab-pane" id="config-formula">
                <div class="alert alert-info py-2">
                    <small><i class="ti ti-info-circle"></i> Use los códigos de examen entre llaves para la fórmula. Ej: <code>{HEM-HB} * 3</code></small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Expresión Matemática</label>
                    <input type="text" class="form-control symbol-input" name="formula_expression"
                        value="<?= htmlspecialchars($formula['formula_expression'] ?? '') ?>" placeholder="Ej: (HTO * 10) / RBC">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Utilidad Clínica del Cálculo</label>
                    <textarea class="form-control" name="clinical_utility" rows="3"><?= htmlspecialchars($formula['clinical_utility'] ?? '') ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Variables Requeridas (CSV)</label>
                        <input type="text" class="form-control" name="required_variables"
                            value="<?= htmlspecialchars($formula['required_variables'] ?? '') ?>" placeholder="HEM-HTO,HEM-RBC">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Decimales de Redondeo</label>
                        <input type="number" class="form-control" name="precision_digits"
                            value="<?= $formula['precision_digits'] ?? 2 ?>" min="0" max="4">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <input type="hidden" name="id" value="<?= $exam['id'] ?>">

    <div class="row">
        <div class="col-md-8 mb-3">
            <label class="form-label fw-bold text-muted">Nombre del Examen</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($exam['name']) ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label fw-bold text-muted">Tipo de Resultado</label>
            <select class="form-select" name="result_type">
                <option value="numeric" <?= ($exam['result_type'] == 'numeric') ? 'selected' : '' ?>>Numérico</option>
                <option value="text" <?= ($exam['result_type'] == 'text') ? 'selected' : '' ?>>Texto</option>
                <option value="options" <?= ($exam['result_type'] == 'options') ? 'selected' : '' ?>>Opciones</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold text-muted">Unidad de Medida</label>
            <input type="text" class="form-control symbol-input" name="unit"
                value="<?= htmlspecialchars($exam['unit']) ?>" autocomplete="off">
        </div>
    </div>

    <!--<div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold text-muted">Código LOINC</label>
            <input type="text" class="form-control" name="loinc_code"
                value="<?= htmlspecialchars($exam['loinc_code'] ?? '') ?>"
                placeholder="Estándar internacional para este examen">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold text-muted">Descripción Clínica</label>
            <textarea class="form-control" name="clinical_description" rows="3"
                placeholder="Definición técnica del examen..."><?= htmlspecialchars($exam['clinical_description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold text-muted">Significancia Clínica</label>
            <textarea class="form-control" name="clinical_significance" rows="3"
                placeholder="¿Qué indica un valor elevado o disminuido?"><?= htmlspecialchars($exam['clinical_significance'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="mt-2 bg-light p-2 rounded border border-dashed">
        <small class="text-muted d-block mb-1"><i class="ti ti-calculator"></i> Convertidor rápido: Años a Días</small>
        <div class="d-flex align-items-center gap-2">
            <input type="number" id="helper_years" class="form-control form-control-sm" style="width: 80px;" placeholder="Años">
            <span class="text-muted">=</span>
            <input type="text" id="helper_days_result" class="form-control form-control-sm" style="width: 100px;" readonly>
            <button type="button" class="btn btn-xs btn-info" id="btnApplyDays" title="Copiar valor">
                <i class="ti ti-copy"></i>
            </button>
        </div>
    </div>-->

    <div class="mt-2">
        <label class="form-label fw-bold text-muted d-flex justify-content-between">
            Rangos de Referencia
            <button type="button" class="btn btn-xs btn-outline-primary shadow-none" id="btnAddRowRange">
                <i class="ti ti-plus"></i> Añadir Rango
            </button>
        </label>

        <div class="table-responsive border rounded" style="max-height: 200px;">
            <table class="table table-sm table-nowrap mb-0" id="tableRanges">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th>Género</th>
                        <th>Edad (Días)</th>
                        <th>Mínimo</th>
                        <th>Máximo</th>
                        <th width="60px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ranges)): ?>
                        <tr class="empty-row text-center">
                            <td colspan="5" class="py-3 text-muted">No hay rangos definidos</td>
                        </tr>
                        <?php else: foreach ($ranges as $range): ?>
                            <tr>
                                <td>
                                    <select name="gender[]" class="form-select form-select-xs border-0 bg-transparent" disabled>
                                        <option value="B" <?= $range['gender'] == 'B' ? 'selected' : '' ?>>Ambos</option>
                                        <option value="M" <?= $range['gender'] == 'M' ? 'selected' : '' ?>>M</option>
                                        <option value="F" <?= $range['gender'] == 'F' ? 'selected' : '' ?>>F</option>
                                    </select>
                                </td>
                                <td><input type="number" name="age_min[]" value="<?= $range['age_min_days'] ?>" class="form-control form-control-xs border-0 bg-transparent" disabled></td>
                                <td><input type="number" name="age_max[]" value="<?= $range['age_max_days'] ?>" class="form-control form-control-xs border-0 bg-transparent" disabled></td>
                                <td><input type="text" name="range_min[]" value="<?= $range['range_min'] ?>" class="form-control form-control-xs border-0 bg-transparent" disabled></td>
                                <td><input type="text" name="range_max[]" value="<?= $range['range_max'] ?>" class="form-control form-control-xs border-0 bg-transparent" disabled></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-link text-primary p-0 btnEditRow" title="Editar"><i class="ti ti-edit"></i></button>
                                    <button type="button" class="btn btn-link text-danger p-0 btnDeleteRow" title="Eliminar"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-end border-top pt-3 mt-3">
        <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary px-4">Guardar Cambios</button>
    </div>
</form>