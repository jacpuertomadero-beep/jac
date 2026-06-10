<section class="tool-panel">
    <div class="tool-panel__header">
        <div>
            <h2>Registro de asistencia</h2>
            <span class="text-muted">Actas de asamblea y participación de afiliados</span>
        </div>

        <button type="button" class="btn btn-primary" id="btnNuevaAsamblea">
            <i class="fas fa-plus"></i>
            Nueva acta
        </button>
    </div>

    <div class="table-responsive table-shell">
        <table class="table table-bordered table-striped table-hover mb-0" id="tablaAsambleas">
            <thead>
                <tr>
                    <th>Ver</th>
                    <th>Acta</th>
                    <th>Fecha asamblea</th>
                    <th>Asistentes</th>
                    <th>Total afiliados</th>
                    <th>Participación</th>
                    <th>Observaciones</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>

<div class="card d-none mt-3" id="cardFormularioAsamblea">
    <div class="card-header">
        <h3 class="card-title" id="tituloFormularioAsamblea">Registrar acta de asamblea</h3>
    </div>

    <form id="formAsamblea">
        <div class="card-body">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="afiliados_asistentes" id="afiliados_asistentes">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="numero_acta">Número de acta</label>
                    <input type="text" class="form-control" name="numero_acta" id="numero_acta" required>
                </div>

                <div class="form-group col-md-4">
                    <label for="fecha_asamblea">Fecha asamblea</label>
                    <input type="text" class="form-control" name="fecha_asamblea" id="fecha_asamblea" autocomplete="off" required>
                </div>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea class="form-control" name="observaciones" id="observaciones" rows="3"></textarea>
            </div>

            <hr>

            <h5>Toma de asistencia</h5>

            <div class="table-responsive">
                <table id="tablaAsistenciaAsamblea" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>Asiste</th>
                            <th>N° Afiliado</th>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Teléfono</th>
                            <th>Comité</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="card-footer text-right">
            <button type="button" class="btn btn-secondary" id="btnCancelarAsamblea">
                Cancelar
            </button>

            <button type="submit" class="btn btn-primary" id="btnGuardarAsamblea">
                Guardar acta
            </button>
        </div>
    </form>
</div>

<div class="card d-none mt-3" id="cardResultadoAsamblea">
    <div class="card-header">
        <h3 class="card-title">Resultado del acta seleccionada</h3>
    </div>

    <div class="card-body" id="detalleResultadoAsamblea">
    </div>
</div>
