<div class="sidenav-menu">

    <a href="<?= BASE_URL ?>dashboard" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="<?= BASE_URL ?>assets/images/logo.png" alt="logo" height="22"></span>
            <span class="logo-sm"><img src="<?= BASE_URL ?>assets/images/logo-sm.png" alt="small logo" height="22"></span>
        </span>
        <span class="logo-dark">
            <span class="logo-lg"><img src="<?= BASE_URL ?>assets/images/logo-dark.png" alt="dark logo" height="22"></span>
            <span class="logo-sm"><img src="<?= BASE_URL ?>assets/images/logo-sm.png" alt="small logo" height="22"></span>
        </span>
    </a>

    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>
        <ul class="side-nav">
            <li class="side-nav-title">Navegación</li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>dashboard" class="side-nav-link">
                    <i class="ti ti-smart-home"></i>
                    <span class="menu-text"> Dashboard </span>
                </a>
            </li>

            <li class="side-nav-title">Gestión Médica</li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>patient" class="side-nav-link">
                    <i class="ti ti-users"></i>
                    <span class="menu-text"> Pacientes </span>
                </a>
            </li>

            <?php if (isset($_SESSION['permisos']['config']) && $_SESSION['permisos']['config']['r']): ?>
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarConfig" aria-expanded="false" aria-controls="sidebarConfig" class="side-nav-link">
                        <i class="ti ti-settings"></i>
                        <span class="menu-text"> Configuración </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarConfig">
                        <ul class="sub-menu">
                            <li class="side-nav-item">
                                <a href="<?= BASE_URL ?>user" class="side-nav-link">Usuarios</a>
                            </li>
                            <li class="side-nav-item">
                                <a href="<?= BASE_URL ?>role" class="side-nav-link">Roles</a>
                            </li>
                        </ul>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>