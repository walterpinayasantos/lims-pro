<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title"><?= $page_title ?></h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <!--<div class="card-header border-bottom border-dashed">
                <h4 class="header-title">Catálogo Maestro de Exámenes</h4>
            </div>-->

            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3 mb-2 mb-sm-0">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <?php $i = 0;
                            foreach ($catalog as $areaName => $exams): $i++; ?>
                                <a class="nav-link <?= ($i === 1) ? 'active show' : '' ?> mb-1"
                                    id="v-pills-area<?= $i ?>-tab"
                                    data-bs-toggle="pill"
                                    href="#v-pills-area<?= $i ?>"
                                    role="tab">
                                    <i class="ti ti-flask fs-18 me-1" style="color: <?= $exams[0]['color_ui'] ?>"></i>
                                    <?= $areaName ?>
                                    <span class="badge bg-primary-subtle text-primary float-end"><?= count($exams) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-sm-9">
                        <div class="tab-content pt-0">
                            <?php $i = 0;
                            foreach ($catalog as $areaName => $exams): $i++; ?>
                                <div class="tab-pane fade <?= ($i === 1) ? 'active show' : '' ?>"
                                    id="v-pills-area<?= $i ?>"
                                    role="tabpanel">

                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0 datatable-exams w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Examen</th>
                                                    <th>Unidad</th>
                                                    <th>Tipo</th>
                                                    <th class="text-center">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($exams as $exam): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="fw-bold"><?= $exam['name'] ?></span>
                                                            <?php if ($exam['is_calculated']): ?>
                                                                <span class="badge bg-info-subtle text-info ms-1">f(x)</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><code><?= $exam['unit'] ?></code></td>
                                                        <td><span class="badge bg-light text-dark"><?= $exam['result_type'] ?></span></td>
                                                        <!--<td class="text-center">
                                                            <a href="<?= BASE_URL ?>exam/edit/<?= $exam['id'] ?>" class="btn btn-xs btn-outline-info">
                                                                <i class="ti ti-settings-automation"></i>
                                                            </a>
                                                        </td>-->
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-xs btn-soft-info btn-edit-exam"
                                                                data-id="<?= $exam['id'] ?>"
                                                                data-bs-toggle="tooltip"
                                                                title="Configurar Examen">
                                                                <i class="ti ti-settings-automation"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditExam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="modalTitle">Editar Examen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyExam">
                <div class="text-center p-3">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>