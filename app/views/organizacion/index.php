<?php
$organizacionActual = $organizacionActual ?? null;
$afiliadosDignatarios = $afiliadosDignatarios ?? [];
$bloquesDignatarios = $bloquesDignatarios ?? [];

$e = static fn (mixed $value): string => htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
$valor = static fn (string $campo): string => (string) ($organizacionActual[$campo] ?? '');
$fecha = static function (string $campoTexto, string $campo) use ($organizacionActual): string {
    return (string) (($organizacionActual[$campoTexto] ?? '') ?: ($organizacionActual[$campo] ?? ''));
};
$seleccionado = static function (string $cargo) use ($organizacionActual): string {
    return (string) ($organizacionActual['dignatarios'][$cargo]['afiliado_id'] ?? '');
};
?>

<form id="formOrganizacion" autocomplete="off">
    <input type="hidden" name="id" id="organizacionId" value="<?= $e($valor('id')) ?>">

    <section class="tool-panel mb-3">
        <div class="tool-panel__header">
            <div>
                <h2>Datos basicos de la organizacion comunal</h2>
                <span class="text-muted">Informacion institucional de la junta de accion comunal</span>
            </div>
            <button type="submit" class="btn btn-primary" id="btnGuardarOrganizacion">
                <i class="fas fa-save"></i>
                Guardar
            </button>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="nombreOrganizacion">Nombre de la organizacion</label>
                    <input type="text" class="form-control" name="nombre" id="nombreOrganizacion" value="<?= $e($valor('nombre')) ?>" maxlength="180" required>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="nitOrganizacion">NIT</label>
                    <input type="text" class="form-control" name="nit" id="nitOrganizacion" value="<?= $e($valor('nit')) ?>" maxlength="40">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="personeriaJuridicaOrganizacion">Personeria juridica</label>
                    <input type="text" class="form-control" name="personeria_juridica" id="personeriaJuridicaOrganizacion" value="<?= $e($valor('personeria_juridica')) ?>" maxlength="100">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="direccionOrganizacion">Direccion</label>
                    <input type="text" class="form-control" name="direccion" id="direccionOrganizacion" value="<?= $e($valor('direccion')) ?>" maxlength="200">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="barrioVeredaOrganizacion">Barrio / vereda</label>
                    <input type="text" class="form-control" name="barrio_vereda" id="barrioVeredaOrganizacion" value="<?= $e($valor('barrio_vereda')) ?>" maxlength="120">
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label for="municipioOrganizacion">Municipio</label>
                    <input type="text" class="form-control" name="municipio" id="municipioOrganizacion" value="<?= $e($valor('municipio')) ?>" maxlength="120">
                </div>
            </div>
            <div class="col-12 col-md-2">
                <div class="form-group">
                    <label for="departamentoOrganizacion">Departamento</label>
                    <input type="text" class="form-control" name="departamento" id="departamentoOrganizacion" value="<?= $e($valor('departamento')) ?>" maxlength="120">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="telefonoOrganizacion">Telefono</label>
                    <input type="text" class="form-control" name="telefono" id="telefonoOrganizacion" value="<?= $e($valor('telefono')) ?>" maxlength="40">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="emailOrganizacion">Correo electronico</label>
                    <input type="email" class="form-control" name="email" id="emailOrganizacion" value="<?= $e($valor('email')) ?>" maxlength="150">
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="periodoInicioOrganizacion">Periodo inicio</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                        </div>
                        <input type="text" class="form-control js-date-organizacion" name="periodo_inicio" id="periodoInicioOrganizacion" value="<?= $e($fecha('periodo_inicio_texto', 'periodo_inicio')) ?>">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="form-group">
                    <label for="periodoFinOrganizacion">Periodo fin</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                        </div>
                        <input type="text" class="form-control js-date-organizacion" name="periodo_fin" id="periodoFinOrganizacion" value="<?= $e($fecha('periodo_fin_texto', 'periodo_fin')) ?>">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tool-panel mb-3">
        <div class="tool-panel__header">
            <div>
                <h2>Resolucion de dignatarios</h2>
                <span class="text-muted">Registro expedido por la alcaldia para el periodo democratico</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="numeroResolucionDignatarios">Numero de resolucion</label>
                    <input type="text" class="form-control" name="numero_resolucion_dignatarios" id="numeroResolucionDignatarios" value="<?= $e($valor('numero_resolucion_dignatarios')) ?>" maxlength="100" required>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="fechaResolucionDignatarios">Fecha de resolucion</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <input type="text" class="form-control js-date-organizacion" name="fecha_resolucion_dignatarios" id="fechaResolucionDignatarios" value="<?= $e($fecha('fecha_resolucion_texto', 'fecha_resolucion_dignatarios')) ?>" required>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label for="urlResolucionDignatarios">Enlace Google Drive</label>
                    <input type="url" class="form-control" name="url_drive_resolucion" id="urlResolucionDignatarios" value="<?= $e($valor('url_drive_resolucion')) ?>">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-0">
                    <label for="observacionesOrganizacion">Observaciones</label>
                    <textarea class="form-control" name="observaciones" id="observacionesOrganizacion" rows="2"><?= $e($valor('observaciones')) ?></textarea>
                </div>
            </div>
        </div>
    </section>

    <?php if (!$afiliadosDignatarios): ?>
        <div class="alert alert-info">
            Registre afiliados antes de seleccionar los dignatarios de la junta.
        </div>
    <?php endif; ?>

    <div class="row organizacion-bloques">
        <?php foreach ($bloquesDignatarios as $bloque => $configuracion): ?>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="<?= $e($configuracion['icono'] ?? 'fas fa-users') ?> mr-1"></i>
                            <?= $e($configuracion['label']) ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($configuracion['cargos'] as $cargo => $label): ?>
                                <div class="col-12 <?= $bloque === 'delegados' ? 'col-md-6' : '' ?>">
                                    <div class="form-group">
                                        <label for="dignatario_<?= $e($cargo) ?>"><?= $e($label) ?></label>
                                        <select class="form-control js-select2-organizacion" name="dignatarios[<?= $e($cargo) ?>]" id="dignatario_<?= $e($cargo) ?>" data-placeholder="Seleccione afiliado" required>
                                            <option value="">Seleccione afiliado</option>
                                            <?php foreach ($afiliadosDignatarios as $afiliado): ?>
                                                <?php
                                                $afiliadoId = (string) ($afiliado['id'] ?? '');
                                                $textoAfiliado = trim(($afiliado['numero_afiliado'] ?? '') . ' - ' . ($afiliado['nombres_completos'] ?? ''));
                                                $textoAfiliado .= ($afiliado['numero_identificacion'] ?? '') !== '' ? ' - ' . $afiliado['numero_identificacion'] : '';
                                                $textoAfiliado .= ($afiliado['estado_afiliacion'] ?? '') === 'desafiliado' ? ' (Desafiliado)' : '';
                                                ?>
                                                <option value="<?= $e($afiliadoId) ?>" <?= $seleccionado($cargo) === $afiliadoId ? 'selected' : '' ?>>
                                                    <?= $e($textoAfiliado) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-actions text-right mb-3">
        <button type="reset" class="btn btn-outline-secondary" id="btnRestablecerOrganizacion">
            <i class="fas fa-undo"></i>
            Restablecer
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            Guardar organizacion
        </button>
    </div>
</form>
