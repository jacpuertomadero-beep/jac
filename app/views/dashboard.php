<div class="row">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-primary">
                <i class="fas fa-users"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Afiliados registrados</span>
                <span class="info-box-number"><?= (int) $totalAfiliados ?></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <a class="btn btn-success dashboard-action" href="<?= BASE_URL ?>index.php?ruta=afiliados">
            <i class="fas fa-user-plus"></i>
            Registrar afiliado
        </a>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <section class="content-band">
            <h2>Gestion de afiliados</h2>
            <p class="mb-0">Use el modulo de afiliados para crear, consultar, editar y eliminar registros de la junta.</p>
        </section>
    </div>
</div>
