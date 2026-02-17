<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h3 class="mt-0">¡Bienvenido, <?= $user_name ?>!</h3>
                        <p class="mb-0">Tu rol actual es: <b><?= $role_name ?></b>. Aquí tienes el resumen de hoy.</p>
                    </div>
                    <div class="flex-shrink-0">
                        <i class="ti ti-report-medical fs-40 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-soft-info rounded">
                        <i class="ti ti-users fs-24 text-info avatar-title"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mt-0 mb-1"><?= $stats['total_patients'] ?></h4>
                        <p class="text-muted mb-0">Pacientes Hoy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-start border-warning border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-soft-warning rounded">
                        <i class="ti ti-flask fs-24 text-warning avatar-title"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mt-0 mb-1"><?= $stats['pending_samples'] ?></h4>
                        <p class="text-muted mb-0">Pendientes de Carga</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-soft-success rounded">
                        <i class="ti ti-signature fs-24 text-success avatar-title"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mt-0 mb-1"><?= $stats['validated_today'] ?></h4>
                        <p class="text-muted mb-0">Validados Hoy</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-start border-danger border-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-soft-danger rounded">
                        <i class="ti ti-alert-triangle fs-24 text-danger avatar-title"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mt-0 mb-1 text-danger"><?= $stats['urgent_tests'] ?></h4>
                        <p class="text-muted mb-0 font-weight-bold">Urgencias</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tareas Recomendadas</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if ($_SESSION['role_id'] == 4 || $_SESSION['role_id'] == 1): // Recepcionista o Admin 
                    ?>
                        <div class="col-md-4">
                            <a href="<?= BASE_URL ?>reception/new" class="btn btn-outline-primary w-100 p-3">
                                <i class="ti ti-user-plus d-block fs-24 mb-1"></i> Nueva Admisión
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 3 || $_SESSION['role_id'] == 1): // Técnico o Admin 
                    ?>
                        <div class="col-md-4">
                            <a href="<?= BASE_URL ?>results/pending" class="btn btn-outline-warning w-100 p-3">
                                <i class="ti ti-microscope d-block fs-24 mb-1"></i> Cargar Resultados
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 1): // Bioquímico o Admin 
                    ?>
                        <div class="col-md-4">
                            <a href="<?= BASE_URL ?>validation/pending" class="btn btn-outline-success w-100 p-3">
                                <i class="ti ti-certificate d-block fs-24 mb-1"></i> Validar Firmas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Estado del Sistema</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Conexión Base de Datos</span>
                    <span class="badge bg-success">Online</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Firma Digital</span>
                    <span class="badge bg-info">Activa</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Versión LIMS</span>
                    <span class="text-muted">v1.0.2-beta</span>
                </div>
            </div>
        </div>
    </div>
</div>