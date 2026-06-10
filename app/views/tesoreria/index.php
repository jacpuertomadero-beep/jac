<section class="tool-panel">
    <div class="tool-panel__header">
        <div>
            <h2>Tesorería</h2>
            <span class="text-muted">Registro de entradas, salidas y saldo de la organización</span>
        </div>

        <button type="button" class="btn btn-primary" id="btnNuevoMovimientoTesoreria">
            <i class="fas fa-plus"></i>
            Nuevo movimiento
        </button>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalEntradasTesoreria">$0</h3>
                    <p>Total entradas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="totalSalidasTesoreria">$0</h3>
                    <p>Total salidas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="saldoTesoreria">$0</h3>
                    <p>Saldo actual</p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card d-none mb-3" id="cardFormularioMovimientoTesoreria">
        <div class="card-header">
            <h3 class="card-title">Registrar movimiento</h3>
        </div>

        <form id="formMovimientoTesoreria">
            <div class="card-body">
                <input type="hidden" name="id" id="tesoreria_id">

                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="tesoreria_fecha">Fecha</label>
                        <input type="text"
                               class="form-control"
                               name="fecha"
                               id="tesoreria_fecha"
                               autocomplete="off"
                               required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="tesoreria_tipo_movimiento">Tipo de movimiento</label>
                        <select class="form-control select2"
                                name="tipo_movimiento"
                                id="tesoreria_tipo_movimiento"
                                required>
                            <option value="">Seleccione...</option>
                            <option value="entrada">Entrada</option>
                            <option value="salida">Salida</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="tesoreria_categoria_id">Categoría</label>
                        <select class="form-control select2"
                                name="categoria_id"
                                id="tesoreria_categoria_id"
                                required>
                            <option value="">Seleccione primero el tipo de movimiento...</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tesoreria_concepto">Concepto</label>
                    <input type="text"
                           class="form-control"
                           name="concepto"
                           id="tesoreria_concepto"
                           maxlength="250"
                           required>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="tesoreria_valor">Valor</label>
                        <input type="number"
                               class="form-control"
                               name="valor"
                               id="tesoreria_valor"
                               min="1"
                               step="0.01"
                               required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tesoreria_medio_pago">Medio de pago</label>
                        <select class="form-control select2"
                                name="medio_pago"
                                id="tesoreria_medio_pago">
                            <option value="">Seleccione...</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="consignacion">Consignación</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="tesoreria_numero_soporte">Número de soporte</label>
                        <input type="text"
                               class="form-control"
                               name="numero_soporte"
                               id="tesoreria_numero_soporte"
                               maxlength="80">
                    </div>
                </div>

                <div class="form-group">
                    <label for="tesoreria_observaciones">Observaciones</label>
                    <textarea class="form-control"
                              name="observaciones"
                              id="tesoreria_observaciones"
                              rows="3"></textarea>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="button" class="btn btn-secondary" id="btnCancelarMovimientoTesoreria">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary">
                    Guardar movimiento
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive table-shell">
        <table class="table table-bordered table-striped table-hover mb-0" id="tablaMovimientosTesoreria">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Categoría</th>
                    <th>Concepto</th>
                    <th>Medio de pago</th>
                    <th>Soporte</th>
                    <th class="text-right">Valor</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>