<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= BASE_URL ?>index.php?ruta=dashboard" class="brand-link">
        <span class="brand-text font-weight-semibold"><?= APP_NAME ?></span>
    </a>

    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=dashboard" class="nav-link <?= $rutaActual === 'dashboard' ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Panel</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=afiliados" class="nav-link <?= str_starts_with($rutaActual, 'afiliado') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Afiliados</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=asambleas" class="nav-link <?= str_starts_with($rutaActual, 'asamblea') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-clipboard-check"></i>
                        <p>Asambleas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=comunicaciones" class="nav-link <?= str_starts_with($rutaActual, 'comunicacion') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>Comunicaciones</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=organizacion" class="nav-link <?= str_starts_with($rutaActual, 'organizacion') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-landmark"></i>
                        <p>Organizacion</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?ruta=tesoreria"
                        class="nav-link <?= str_starts_with($rutaActual, 'tesoreria') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-wallet"></i>
                        <p>Tesorería</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>