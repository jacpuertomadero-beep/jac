<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item mr-3 d-none d-sm-block">
                <span class="text-muted small"><?= htmlspecialchars($usuario['nombres'], ENT_QUOTES, 'UTF-8') ?></span>
            </li>
            <li class="nav-item">
                <a class="btn btn-outline-danger btn-sm" href="<?= BASE_URL ?>index.php?ruta=logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Salir
                </a>
            </li>
        </ul>
    </div>
</nav>
