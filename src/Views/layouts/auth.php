<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require_once SRC_PATH . 'Views/partials/_head.php'; ?>
    <title><?= $page_title ?? 'LIMS Pro' ?> | Acceso</title>
</head>

<body class="authentication-bg">

    <div class="account-pages my-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <main>
                        <?php require_once $contentPath; ?>
                    </main>
                </div>
            </div>
        </div>
    </div>

    <?php require_once SRC_PATH . 'Views/partials/_scripts.php'; ?>

    <?php if (isset($extra_js) && is_array($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?= BASE_URL . $js ?>?v=<?= time() ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>