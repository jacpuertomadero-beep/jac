<section class="tool-panel">
    <div class="tool-panel__header">
        <div>
            <h2>Gestion documental</h2>
            <span class="text-muted">Comunicaciones enviadas y recibidas con archivo digital en Google Drive</span>
        </div>
        <button type="button" class="btn btn-primary" id="btnNuevaComunicacion">
            <i class="fas fa-plus"></i>
            Nueva comunicacion
        </button>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col-12 col-md-3">
            <div class="form-group mb-md-0">
                <label for="filtroTipoComunicacion">Tipo</label>
                <select class="form-control" id="filtroTipoComunicacion">
                    <option value="">Todos</option>
                    <?php foreach ($tiposComunicacion as $valor => $label): ?>
                        <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group mb-md-0">
                <label for="filtroMedioComunicacion">Medio</label>
                <select class="form-control" id="filtroMedioComunicacion">
                    <option value="">Todos</option>
                    <?php foreach ($mediosRadicacion as $valor => $label): ?>
                        <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="form-group mb-md-0">
                <label for="filtroRespuestaComunicacion">Respuesta</label>
                <select class="form-control" id="filtroRespuestaComunicacion">
                    <option value="">Todas</option>
                    <option value="sin_respuesta">Sin respuesta</option>
                    <option value="respondidas">Respondidas</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltrosComunicacion">
                <i class="fas fa-times-circle"></i>
                Limpiar filtros
            </button>
        </div>
    </div>

    <div class="table-responsive table-shell">
        <table class="table table-bordered table-striped table-hover mb-0" id="tablaComunicaciones">
            <thead>
            <tr>
                <th>Tipo</th>
                <th>Asunto</th>
                <th>Destinatario / remitente</th>
                <th>Radicado</th>
                <th>Fecha radicacion</th>
                <th>Medio</th>
                <th>Comunicacion</th>
                <th>Seguimiento</th>
                <th>Respuesta</th>
                <th>Dias habiles</th>
                <th class="text-right">Acciones</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>

<div class="card d-none mt-3" id="cardFormularioComunicacion">
    <div class="card-header">
        <h3 class="card-title" id="tituloFormularioComunicacion">Registrar comunicacion</h3>
    </div>

    <form id="formComunicacion" autocomplete="off">
        <div class="card-body">
            <input type="hidden" name="id" id="comunicacionId">

            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="tipoComunicacion">Tipo</label>
                        <select class="form-control" name="tipo_comunicacion" id="tipoComunicacion" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($tiposComunicacion as $valor => $label): ?>
                                <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <div class="form-group">
                        <label for="asuntoComunicacion">Asunto</label>
                        <input type="text" class="form-control" name="asunto" id="asuntoComunicacion" maxlength="200" required>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="terceroComunicacion">Destinatario / remitente</label>
                        <input type="text" class="form-control" name="tercero" id="terceroComunicacion" maxlength="180" required>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fechaRadicadoComunicacion">Fecha de radicacion</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                            </div>
                            <input type="text" class="form-control" name="fecha_radicado" id="fechaRadicadoComunicacion" required>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="numeroRadicadoComunicacion">Numero de radicado</label>
                        <input type="text" class="form-control" name="numero_radicado" id="numeroRadicadoComunicacion" maxlength="80">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="medioRadicacionComunicacion">Medio de radicacion</label>
                        <select class="form-control" name="medio_radicacion" id="medioRadicacionComunicacion" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($mediosRadicacion as $valor => $label): ?>
                                <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="fechaRespuestaComunicacion">Fecha de respuesta</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                            </div>
                            <input type="text" class="form-control" name="fecha_respuesta" id="fechaRespuestaComunicacion">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="urlDriveComunicacion">Enlace Google Drive de la comunicacion</label>
                        <input type="url" class="form-control" name="url_drive_comunicacion" id="urlDriveComunicacion" required>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="seguimientoComunicacion">Seguimiento</label>
                        <textarea class="form-control" name="seguimiento" id="seguimientoComunicacion" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="urlDriveSeguimientoComunicacion">Enlace Drive seguimiento</label>
                        <input type="url" class="form-control" name="url_drive_seguimiento" id="urlDriveSeguimientoComunicacion">
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="form-group">
                        <label for="respuestaComunicacion">Respuesta</label>
                        <textarea class="form-control" name="respuesta" id="respuestaComunicacion" rows="3"></textarea>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label for="urlDriveRespuestaComunicacion">Enlace Drive respuesta</label>
                        <input type="url" class="form-control" name="url_drive_respuesta" id="urlDriveRespuestaComunicacion">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="observacionesComunicacion">Observaciones</label>
                        <textarea class="form-control" name="observaciones" id="observacionesComunicacion" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <button type="button" class="btn btn-outline-secondary" id="btnCancelarComunicacion">
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary" id="btnGuardarComunicacion">
                <i class="fas fa-save"></i>
                Guardar
            </button>
        </div>
    </form>
</div>
