<header class="app-topbar">
    <div class="page-container topbar-menu">
        <div class="d-flex align-items-center gap-2">

            <a href="<?= BASE_URL ?>dashboard" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="<?= BASE_URL ?>assets/images/logo.png" alt="logo" /></span>
                    <span class="logo-sm"><img src="<?= BASE_URL ?>assets/images/logo-sm.png" alt="small logo" /></span>
                </span>

                <span class="logo-dark">
                    <span class="logo-lg"><img src="<?= BASE_URL ?>assets/images/logo-dark.png" alt="dark logo" /></span>
                    <span class="logo-sm"><img src="<?= BASE_URL ?>assets/images/logo-sm.png" alt="small logo" /></span>
                </span>
            </a>

            <button class="sidenav-toggle-button px-2">
                <i class="ti ti-menu-deep fs-24"></i>
            </button>

            <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <i class="ti ti-menu-deep fs-22"></i>
            </button>

            <div class="topbar-search text-muted d-none d-xl-flex gap-2 align-items-center" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                <i class="ti ti-search fs-18"></i>
                <span class="me-2">Buscar...</span>
                <span class="ms-auto fw-medium">⌘K</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">

            <div class="topbar-item d-flex d-xl-none">
                <button class="topbar-link" data-bs-toggle="modal" data-bs-target="#searchModal" type="button">
                    <i class="ti ti-search fs-22"></i>
                </button>
            </div>

            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-22"></i>
                </button>
            </div>

            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" data-bs-offset="0,19" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="<?= BASE_URL ?>assets/images/users/<?= $_SESSION['avatar'] ?? 'avatar-1.jpg' ?>" width="32" class="rounded-circle me-lg-2 d-flex" alt="user-image" />
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h5 class="my-0"><?= $_SESSION['full_name'] ?? 'Usuario' ?></h5>
                            <h6 class="my-0 fw-normal"><?= $_SESSION['role_name'] ?? 'Personal' ?></h6>
                        </span>
                        <i class="ti ti-chevron-down d-none d-lg-block align-middle ms-2"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">¡Bienvenido!</h6>
                        </div>

                        <a href="<?= BASE_URL ?>user/profile" class="dropdown-item">
                            <i class="ti ti-user-hexagon me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Mi Cuenta</span>
                        </a>

                        <a href="javascript:void(0);" class="dropdown-item">
                            <i class="ti ti-settings me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Configuración</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a href="<?= BASE_URL ?>auth/logout" class="dropdown-item fw-semibold text-danger">
                            <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                            <span class="align-middle">Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>