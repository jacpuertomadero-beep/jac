<section class="tool-panel">
    <div class="tool-panel__header">
        <div>
            <h2>Registro de afiliados</h2>
            <span class="text-muted">Junta de accion comunal</span>
        </div>
        <button type="button" class="btn btn-primary" id="btnNuevoAfiliado">
            <i class="fas fa-plus"></i>
            Nuevo
        </button>
    </div>

    <div class="row align-items-end mb-3">
        <div class="col-12 col-lg-4">
            <label class="form-label" for="buscarAfiliado">Buscar</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
                <input type="search" class="form-control" id="buscarAfiliado" placeholder="Nombre, afiliado o identificacion">
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <label class="form-label" for="filtroTipoIdentificacion">Tipo</label>
            <select class="form-control js-select2" id="filtroTipoIdentificacion">
                <option value="">Todos</option>
                <?php foreach ($tiposIdentificacion as $valor => $label): ?>
                    <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-6 col-lg-3">
            <label class="form-label" for="filtroFechas">Fecha de registro</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="text" class="form-control" id="filtroFechas" readonly>
            </div>
        </div>
        <div class="col-12 col-lg-2">
            <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros">
                <i class="fas fa-times-circle"></i>
                Limpiar
            </button>
        </div>
    </div>

    <div class="table-responsive table-shell">
        <table class="table table-bordered table-striped table-hover mb-0" id="tablaAfiliados">
            <thead>
            <tr>
                <th>Afiliado</th>
                <th class="col-fecha-afiliacion">Fecha afiliacion</th>
                <th>Nombres</th>
                <th>Edad</th>
                <th>Identificacion</th>
                <th>Direccion</th>
                <th>Comite</th>
                <th>Telefono</th>
                <th>Estado</th>
                <th class="text-right">Acciones</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>

<div class="modal fade" id="modalAfiliado" tabindex="-1" aria-labelledby="modalAfiliadoTitulo" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="formAfiliado" autocomplete="off">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="modalAfiliadoTitulo">Registrar afiliado</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="afiliadoId">

                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="numeroAfiliado">Numero de afiliado</label>
                                <input type="text" class="form-control" name="numero_afiliado" id="numeroAfiliado" maxlength="30" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="fechaAfiliacion">Fecha de afiliacion</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                    </div>
                                    <input type="text" class="form-control" name="fecha_afiliacion" id="fechaAfiliacion" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="nombresCompletos">Nombres completos</label>
                                <input type="text" class="form-control" name="nombres_completos" id="nombresCompletos" maxlength="150" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="edad">Edad</label>
                                <input type="number" class="form-control" name="edad" id="edad" min="0" max="120" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="tipoIdentificacion">Tipo de identificacion</label>
                                <select class="form-control js-select2-modal" name="tipo_identificacion" id="tipoIdentificacion" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($tiposIdentificacion as $valor => $label): ?>
                                        <option value="<?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="numeroIdentificacion">Numero de identificacion</label>
                                <input type="text" class="form-control" name="numero_identificacion" id="numeroIdentificacion" maxlength="30" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label for="direccion">Direccion</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" maxlength="200" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="comiteTrabajo">Comite de trabajo</label>
                                <select class="form-control js-select2-modal" name="comite_trabajo" id="comiteTrabajo" required>
                                    <option value="">Seleccione</option>
                                    <?php foreach ($comites as $comite): ?>
                                        <option value="<?= htmlspecialchars($comite, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($comite, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="telefono">Telefono</label>
                                <input type="tel" class="form-control" name="telefono" id="telefono" maxlength="30">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control" name="observaciones" id="observaciones" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="estadoAfiliacion">Estado del afiliado</label>
                                <select class="form-control js-select2-modal" name="estado_afiliacion" id="estadoAfiliacion" required>
                                    <option value="afiliado">Afiliado</option>
                                    <option value="desafiliado">Desafiliado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 desafiliacion-fields d-none">
                            <div class="form-group">
                                <label for="mesesSancion">Meses de sancion</label>
                                <input type="number" class="form-control" name="meses_sancion" id="mesesSancion" min="1" max="36">
                            </div>
                        </div>
                        <div class="col-12 desafiliacion-fields d-none">
                            <div class="form-group">
                                <label for="actaFalloEdicto">Acta o fallo del edicto</label>
                                <textarea class="form-control" name="acta_fallo_edicto" id="actaFalloEdicto" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarAfiliado">
                        <i class="fas fa-save"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
