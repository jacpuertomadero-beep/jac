<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesion | <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/app.css">
</head>
<body class="hold-transition login-page">
<main class="login-box">
    <div class="login-logo">
        <strong><?= APP_NAME ?></strong>
    </div>
    <div class="card shadow-sm">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Ingreso de administrador</p>

            <form id="formLogin" autocomplete="off">
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Contrasena" required>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sign-in-alt"></i>
                    Entrar
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    window.APP_BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= BASE_URL ?>public/assets/js/app.js"></script>
</body>
</html>
