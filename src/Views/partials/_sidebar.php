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

            <li class="side-nav-title">Recepción</li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>patient" class="side-nav-link">
                    <i class="ti ti-users"></i>
                    <span class="menu-text"> Pacientes </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarOrders" aria-expanded="false" aria-controls="sidebarOrders" class="side-nav-link">
                    <i class="ti ti-clipboard-heart"></i>
                    <span class="menu-text"> Gestión de Órdenes </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarOrders">
                    <ul class="sub-menu">
                        <li class="side-nav-item">
                            <a href="<?= BASE_URL ?>order/create" class="side-nav-link">Nueva Admisión</a>
                        </li>
                        <li class="side-nav-item">
                            <a href="<?= BASE_URL ?>order" class="side-nav-link">Listado / Caja</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-title">Laboratorio</li>


            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>sample" class="side-nav-link">
                    <i class="ti ti-vaccine"></i> <span class="menu-text"> Toma de Muestras </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>result" class="side-nav-link">
                    <i class="ti ti-microscope"></i> <span class="menu-text"> Resultados / Validación </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>result/signature" class="side-nav-link">
                    <i class="ti ti-signature"></i> <span class="menu-text"> Firma Facultativa </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>result/delivery" class="side-nav-link">
                    <i class="ti ti-certificate"></i> <span class="menu-text">Entrega Resultados</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>result/history" class="side-nav-link">
                    <i class="ti ti-history"></i>
                    <span class="menu-text">Historial Entregas</span>
                </a>
            </li>

            <li class="side-nav-title">Configuración</li>

            <li class="side-nav-item">
                <a href="<?= BASE_URL ?>exam" class="side-nav-link">
                    <i class="ti ti-flask"></i>
                    <span class="menu-text"> Catálogo Exámenes </span>
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