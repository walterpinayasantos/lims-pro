<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Historial Clínico</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <?= substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1) ?>
                </div>
                <h4 class="mt-0"><?= $patient['first_name'] ?> <?= $patient['last_name'] ?></h4>
                <p class="text-muted"><?= $patient['document_id'] ?></p>
                <div class="text-start mt-3">
                    <p><strong><i class="ti ti-calendar"></i> Nacimiento:</strong> <?= $patient['birth_date'] ?></p>
                    <p><strong><i class="ti ti-phone"></i> Teléfono:</strong> <?= $patient['phone'] ?: 'N/A' ?></p>
                    <p><strong><i class="ti ti-mail"></i> Email:</strong> <?= $patient['email'] ?: 'N/A' ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title mb-3">Línea de Tiempo de Estudios</h5>

                <div class="alert alert-info" role="alert">
                    <i class="ti ti-info-circle me-1"></i> Este paciente aún no tiene órdenes registradas en el sistema.
                    <br>
                    Cuando registremos órdenes en la <strong>Fase Pre-Analítica</strong>, aparecerán aquí cronológicamente.
                </div>

            </div>
        </div>
    </div>
</div>