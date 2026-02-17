<!DOCTYPE html>
<html lang="es" data-topbar-color="dark" data-menu-color="light" data-sidenav-size="default">

<head>
    <?php require_once SRC_PATH . 'Views/partials/_head.php'; ?>
</head>

<body>
    <div class="wrapper">

        <?php require_once SRC_PATH . 'Views/partials/_sidebar.php'; ?>
        <?php require_once SRC_PATH . 'Views/partials/_topbar.php'; ?>
        <?php require_once SRC_PATH . 'Views/partials/_search_modal.php'; ?>
        <div class="page-content">
            <div class="page-container">
                <div class="page-title-head d-flex align-items-center gap-2">
                    <div class="flex-grow-1">
                        <h4 class="fs-16 text-uppercase fw-bold mb-0">
                            <?= $page_title ?? 'LIMS Pro' ?>
                        </h4>
                    </div>

                    <div class="text-end">
                        <ol class="breadcrumb m-0 py-0 fs-13">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_URL ?>dashboard">LIMS</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <?= $page_title ?? 'Inicio' ?>
                            </li>
                        </ol>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php require_once $contentPath; ?>
                    </div>
                </div>
            </div>
            <?php require_once SRC_PATH . 'Views/partials/_footer.php'; ?>
        </div>
    </div>
    <?php require_once SRC_PATH . 'Views/partials/_theme_settings.php'; ?>
    <?php require_once SRC_PATH . 'Views/partials/_scripts.php'; ?>

    <?php if (isset($extra_js) && is_array($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?= BASE_URL . $js ?>?v=<?= time() ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>