(function ($) {
    'use strict';

    const baseUrl = window.APP_BASE_URL || '/';
    let afiliados = [];
    let filtroFechas = { inicio: null, fin: null };
    let tablaAfiliados = null;
    let filtroDataTablesRegistrado = false;

    const endpoints = {
        login: `${baseUrl}index.php?ruta=loginValidar`,
        listar: `${baseUrl}index.php?ruta=afiliadoListar`,
        guardar: `${baseUrl}index.php?ruta=afiliadoGuardar`,
        editar: `${baseUrl}index.php?ruta=afiliadoEditar`,
        eliminar: `${baseUrl}index.php?ruta=afiliadoEliminar`,
        asambleaListar: `${baseUrl}index.php?ruta=asambleaListar`,
        asambleaGuardar: `${baseUrl}index.php?ruta=asambleaGuardar`,
        asambleaEditar: `${baseUrl}index.php?ruta=asambleaEditar`,
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function notify(icon, title) {
        Swal.fire({
            icon,
            title,
            timer: 2200,
            showConfirmButton: false,
        });
    }

    function toggleButton($button, loading) {
        $button.prop('disabled', loading);
        $button.data('original-text', $button.data('original-text') || $button.html());
        $button.html(loading ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Procesando' : $button.data('original-text'));
    }

    function initPlugins() {
        if ($.fn.select2) {
            $('.js-select2').select2({
                theme: 'bootstrap4',
                width: '100%',
            });

            $('.js-select2-modal').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#modalAfiliado'),
            });

        }

        if ($('#filtroFechas').length && $.fn.daterangepicker) {
            $('#filtroFechas').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Limpiar',
                    applyLabel: 'Aplicar',
                    format: 'YYYY-MM-DD',
                },
            });

            $('#filtroFechas').on('apply.daterangepicker', function (_event, picker) {
                filtroFechas = {
                    inicio: picker.startDate.startOf('day').clone(),
                    fin: picker.endDate.endOf('day').clone(),
                };
                $(this).val(`${picker.startDate.format('YYYY-MM-DD')} - ${picker.endDate.format('YYYY-MM-DD')}`);
                redibujarTablaAfiliados();
            });

            $('#filtroFechas').on('cancel.daterangepicker', function () {
                filtroFechas = { inicio: null, fin: null };
                $(this).val('');
                redibujarTablaAfiliados();
            });
        }

        if ($('#fechaAfiliacion').length && $.fn.daterangepicker) {
            $('#fechaAfiliacion').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: true,
                startDate: moment(),
                minYear: 1900,
                maxYear: parseInt(moment().format('YYYY'), 10) + 10,
                locale: {
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    format: 'YYYY-MM-DD',
                },
            });
        }

    }

    function registrarFiltroDataTables() {
        if (!$.fn.dataTable || filtroDataTablesRegistrado) {
            return;
        }

        $.fn.dataTable.ext.search.push(function (settings, _data, dataIndex, rowData) {
            if (settings.nTable.id !== 'tablaAfiliados') {
                return true;
            }

            const item = rowData || (tablaAfiliados ? tablaAfiliados.row(dataIndex).data() : null);
            if (!item) {
                return true;
            }

            const tipo = $('#filtroTipoIdentificacion').val();
            if (tipo && item.tipo_identificacion !== tipo) {
                return false;
            }

            if (filtroFechas.inicio && filtroFechas.fin && item.creado_en) {
                const fecha = moment(item.creado_en);
                if (!fecha.isBetween(filtroFechas.inicio, filtroFechas.fin, undefined, '[]')) {
                    return false;
                }
            }

            return true;
        });

        filtroDataTablesRegistrado = true;
    }

    function inicializarDataTableAfiliados() {
        if (!$('#tablaAfiliados').length || !$.fn.DataTable) {
            return false;
        }

        if ($.fn.dataTable.isDataTable('#tablaAfiliados')) {
            tablaAfiliados.ajax.reload(null, false);
            return true;
        }

        registrarFiltroDataTables();

        tablaAfiliados = $('#tablaAfiliados').DataTable({
            ajax: {
                url: endpoints.listar,
                dataSrc: 'data',
            },
            columns: [
                {
                    data: 'numero_afiliado',
                    render: function (data, type) {
                        return type === 'display' ? `<strong>${escapeHtml(data)}</strong>` : (data || '');
                    },
                },
                {
                    data: null,
                    render: function (_data, _type, row) {
                        return escapeHtml(row.fecha_afiliacion_texto || row.fecha_afiliacion || '-');
                    },
                },
                {
                    data: 'nombres_completos',
                    render: function (data) {
                        return escapeHtml(data);
                    },
                },
                {
                    data: 'edad',
                    render: function (data) {
                        return escapeHtml(data);
                    },
                },
                {
                    data: null,
                    render: function (_data, type, row) {
                        if (type !== 'display') {
                            return `${row.tipo_identificacion || ''} ${row.numero_identificacion || ''}`.trim();
                        }

                        return `
                            <span class="badge badge-light border">${escapeHtml(row.tipo_identificacion)}</span>
                            ${escapeHtml(row.numero_identificacion)}
                        `;
                    },
                },
                {
                    data: 'direccion',
                    render: function (data) {
                        return escapeHtml(data);
                    },
                },
                {
                    data: 'comite_trabajo',
                    render: function (data) {
                        return escapeHtml(data);
                    },
                },
                {
                    data: 'telefono',
                    render: function (data) {
                        return escapeHtml(data || '-');
                    },
                },
                {
                    data: 'estado_afiliacion',
                    render: function (data, type) {
                        if (type !== 'display') {
                            return data === 'desafiliado' ? 'Desafiliado' : 'Afiliado';
                        }

                        return renderEstadoAfiliacion(data);
                    },
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function (id) {
                        return `
                            <span class="row-actions">
                                <button type="button" class="btn btn-outline-primary btn-editar" data-id="${id}" title="Editar" aria-label="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${id}" title="Eliminar" aria-label="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </span>
                        `;
                    },
                },
            ],
            deferRender: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'desc']],
            columnDefs: [
                {
                    targets: 1,
                    width: '145px',
                    className: 'col-fecha-afiliacion text-nowrap',
                },
            ],
            dom: "<'row mb-2'<'col-sm-12'l>>rt<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                decimal: '',
                emptyTable: 'No hay registros para mostrar.',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ afiliados',
                infoEmpty: 'Mostrando 0 afiliados',
                infoFiltered: '(filtrado de _MAX_ afiliados)',
                lengthMenu: 'Mostrar _MENU_ registros',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: 'Buscar:',
                zeroRecords: 'No se encontraron registros.',
                paginate: {
                    first: 'Primero',
                    last: 'Ultimo',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
            },
        });

        return true;
    }

    function redibujarTablaAfiliados() {
        if (tablaAfiliados) {
            tablaAfiliados.draw();
            return;
        }

        renderAfiliados();
    }

    function recargarTablaAfiliados() {
        if (tablaAfiliados) {
            tablaAfiliados.ajax.reload(null, false);
            return;
        }

        cargarAfiliados();
    }

    function cargarAfiliados() {
        if (!$('#tablaAfiliados').length) {
            return;
        }

        if (inicializarDataTableAfiliados()) {
            return;
        }

        $.getJSON(endpoints.listar)
            .done(function (response) {
                afiliados = response.data || [];
                renderAfiliados();
            })
            .fail(function () {
                notify('error', 'No fue posible cargar afiliados.');
            });
    }

    function inicializarDataTableAsambleas() {
        if (!$('#tablaAsambleas').length || !$.fn.DataTable) {
            return false;
        }

        if ($.fn.dataTable.isDataTable('#tablaAsambleas')) {
            $('#tablaAsambleas').DataTable().ajax.reload(null, false);
            return true;
        }

        $('#tablaAsambleas').DataTable({
            ajax: {
                url: endpoints.asambleaListar,
                dataSrc: 'data',
            },
            columns: [
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function (id) {
                        return `
                            <button type="button" class="btn btn-outline-info btn-ver-asamblea" data-id="${id}" title="Ver asistencia" aria-label="Ver asistencia">
                                <i class="fas fa-eye"></i>
                            </button>
                        `;
                    },
                },
                {
                    data: 'numero_acta',
                    render: function (data, type) {
                        return type === 'display' ? `<strong>${escapeHtml(data)}</strong>` : (data || '');
                    },
                },
                {
                    data: null,
                    render: function (_data, _type, row) {
                        return escapeHtml(row.fecha_asamblea_texto || row.fecha_asamblea || '-');
                    },
                },
                {
                    data: 'asistentes',
                    className: 'text-center',
                    render: function (data) {
                        return escapeHtml(data || 0);
                    },
                },
                {
                    data: 'total_afiliados',
                    className: 'text-center',
                    render: function (data) {
                        return escapeHtml(data || 0);
                    },
                },
                {
                    data: 'porcentaje_participacion',
                    render: function (data, type) {
                        if (type !== 'display') {
                            return Number(data || 0);
                        }

                        return renderPorcentajeParticipacion(data);
                    },
                },
                {
                    data: 'observaciones',
                    render: function (data) {
                        return escapeHtml(data || '-');
                    },
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-right',
                    render: function (id) {
                        return `
                            <span class="row-actions">
                                <button type="button" class="btn btn-outline-primary btn-editar-asamblea" data-id="${id}" title="Editar" aria-label="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </span>
                        `;
                    },
                },
            ],
            deferRender: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[2, 'desc']],
            language: {
                decimal: '',
                emptyTable: 'No hay actas registradas.',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ actas',
                infoEmpty: 'Mostrando 0 actas',
                infoFiltered: '(filtrado de _MAX_ actas)',
                lengthMenu: 'Mostrar _MENU_ registros',
                loadingRecords: 'Cargando...',
                processing: 'Procesando...',
                search: 'Buscar:',
                zeroRecords: 'No se encontraron actas.',
                paginate: {
                    first: 'Primero',
                    last: 'Ultimo',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
            },
        });

        return true;
    }

    function cargarAsambleas() {
        inicializarDataTableAsambleas();
    }

    function afiliadoCumpleFiltros(item) {
        const texto = ($('#buscarAfiliado').val() || '').toLowerCase().trim();
        const tipo = $('#filtroTipoIdentificacion').val();

        const contenido = [
            item.numero_afiliado,
            item.fecha_afiliacion,
            item.nombres_completos,
            item.numero_identificacion,
            item.direccion,
            item.comite_trabajo,
            item.telefono,
            item.estado_afiliacion,
        ].join(' ').toLowerCase();

        if (texto && !contenido.includes(texto)) {
            return false;
        }

        if (tipo && item.tipo_identificacion !== tipo) {
            return false;
        }

        if (filtroFechas.inicio && filtroFechas.fin && item.creado_en) {
            const fecha = moment(item.creado_en);
            if (!fecha.isBetween(filtroFechas.inicio, filtroFechas.fin, undefined, '[]')) {
                return false;
            }
        }

        return true;
    }

    function renderAfiliados() {
        const $tbody = $('#tablaAfiliados tbody');
        const visibles = afiliados.filter(afiliadoCumpleFiltros);

        if (!visibles.length) {
            $tbody.html('<tr><td colspan="10" class="text-center text-muted py-4">No hay registros para mostrar.</td></tr>');
            return;
        }

        const rows = visibles.map(function (item) {
            return `
                <tr>
                    <td><strong>${escapeHtml(item.numero_afiliado)}</strong></td>
                    <td>${escapeHtml(item.fecha_afiliacion_texto || item.fecha_afiliacion || '-')}</td>
                    <td>${escapeHtml(item.nombres_completos)}</td>
                    <td>${escapeHtml(item.edad)}</td>
                    <td>
                        <span class="badge badge-light border">${escapeHtml(item.tipo_identificacion)}</span>
                        ${escapeHtml(item.numero_identificacion)}
                    </td>
                    <td>${escapeHtml(item.direccion)}</td>
                    <td>${escapeHtml(item.comite_trabajo)}</td>
                    <td>${escapeHtml(item.telefono || '-')}</td>
                    <td>${renderEstadoAfiliacion(item.estado_afiliacion)}</td>
                    <td class="text-right">
                        <span class="row-actions">
                            <button type="button" class="btn btn-outline-primary btn-editar" data-id="${item.id}" title="Editar" aria-label="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-eliminar" data-id="${item.id}" title="Eliminar" aria-label="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </span>
                    </td>
                </tr>
            `;
        });

        $tbody.html(rows.join(''));
    }

    function renderEstadoAfiliacion(estado) {
        const estadoNormalizado = estado === 'desafiliado' ? 'desafiliado' : 'afiliado';
        const clase = estadoNormalizado === 'desafiliado' ? 'badge-danger' : 'badge-success';
        const texto = estadoNormalizado === 'desafiliado' ? 'Desafiliado' : 'Afiliado';

        return `<span class="badge ${clase}">${texto}</span>`;
    }

    function renderPorcentajeParticipacion(valor) {
        const porcentaje = Math.min(100, Math.max(0, Number(valor || 0)));
        const porcentajeTexto = porcentaje.toFixed(2).replace(/\.00$/, '');

        return `
            <div class="participacion-cell">
                <strong>${porcentajeTexto}%</strong>
                <div class="progress progress-xs mt-1">
                    <div class="progress-bar bg-success" style="width: ${porcentaje}%"></div>
                </div>
            </div>
        `;
    }

    function toggleCamposDesafiliacion() {
        const esDesafiliado = $('#estadoAfiliacion').val() === 'desafiliado';

        $('.desafiliacion-fields').toggleClass('d-none', !esDesafiliado);
        $('#actaFalloEdicto').prop('required', esDesafiliado);
        $('#mesesSancion').prop('required', esDesafiliado);

        if (!esDesafiliado) {
            $('#actaFalloEdicto').val('');
            $('#mesesSancion').val('');
        }
    }

    function limpiarFormularioAfiliado() {
        const form = document.getElementById('formAfiliado');
        if (!form) {
            return;
        }

        form.reset();
        $('#afiliadoId').val('');
        $('#fechaAfiliacion').val(moment().format('YYYY-MM-DD'));
        if ($('#fechaAfiliacion').data('daterangepicker')) {
            $('#fechaAfiliacion').data('daterangepicker').setStartDate(moment());
            $('#fechaAfiliacion').data('daterangepicker').setEndDate(moment());
        }
        $('.js-select2-modal').val('').trigger('change');
        $('#estadoAfiliacion').val('afiliado').trigger('change');
        $('#actaFalloEdicto').val('');
        $('#mesesSancion').val('');
        toggleCamposDesafiliacion();
        $('#modalAfiliadoTitulo').text('Registrar afiliado');
    }

    function llenarFormularioAfiliado(data) {
        $('#afiliadoId').val(data.id);
        $('#numeroAfiliado').val(data.numero_afiliado);
        $('#fechaAfiliacion').val(data.fecha_afiliacion);
        if ($('#fechaAfiliacion').data('daterangepicker')) {
            $('#fechaAfiliacion').data('daterangepicker').setStartDate(moment(data.fecha_afiliacion));
            $('#fechaAfiliacion').data('daterangepicker').setEndDate(moment(data.fecha_afiliacion));
        }
        $('#nombresCompletos').val(data.nombres_completos);
        $('#edad').val(data.edad);
        $('#tipoIdentificacion').val(data.tipo_identificacion).trigger('change');
        $('#numeroIdentificacion').val(data.numero_identificacion);
        $('#direccion').val(data.direccion);
        $('#comiteTrabajo').val(data.comite_trabajo).trigger('change');
        $('#telefono').val(data.telefono);
        $('#observaciones').val(data.observaciones);
        $('#estadoAfiliacion').val(data.estado_afiliacion || 'afiliado').trigger('change');
        $('#actaFalloEdicto').val(data.acta_fallo_edicto || '');
        $('#mesesSancion').val(data.meses_sancion || '');
        toggleCamposDesafiliacion();
        $('#modalAfiliadoTitulo').text('Editar afiliado');
    }

    function abrirModalAfiliado() {
        $('#modalAfiliado').modal('show');
    }

    function recargarTablaAsambleas() {
        if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tablaAsambleas')) {
            $('#tablaAsambleas').DataTable().ajax.reload(null, false);
        }
    }

    function registrarEventos() {
        $('#formLogin').on('submit', function (event) {
            event.preventDefault();

            const $button = $(this).find('button[type="submit"]');
            toggleButton($button, true);

            $.ajax({
                url: endpoints.login,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
            })
                .done(function (response) {
                    if (response.ok) {
                        window.location.href = response.redirect;
                        return;
                    }
                    notify('error', response.message || 'No fue posible iniciar sesion.');
                })
                .fail(function (xhr) {
                    notify('error', xhr.responseJSON?.message || 'No fue posible iniciar sesion.');
                })
                .always(function () {
                    toggleButton($button, false);
                });
        });

        $('#btnNuevoAfiliado').on('click', function () {
            limpiarFormularioAfiliado();
            abrirModalAfiliado();
        });

        $('#formAfiliado').on('submit', function (event) {
            event.preventDefault();

            const $button = $('#btnGuardarAfiliado');
            toggleButton($button, true);

            $.ajax({
                url: endpoints.guardar,
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
            })
                .done(function (response) {
                    notify('success', response.message || 'Registro guardado.');
                    $('#modalAfiliado').modal('hide');
                    recargarTablaAfiliados();
                })
                .fail(function (xhr) {
                    notify('error', xhr.responseJSON?.message || 'No fue posible guardar.');
                })
                .always(function () {
                    toggleButton($button, false);
                });
        });

        $('#tablaAfiliados').on('click', '.btn-editar', function () {
            const id = $(this).data('id');

            $.getJSON(`${endpoints.editar}&id=${id}`)
                .done(function (response) {
                    if (!response.ok) {
                        notify('error', response.message || 'Afiliado no encontrado.');
                        return;
                    }

                    llenarFormularioAfiliado(response.data);
                    abrirModalAfiliado();
                })
                .fail(function (xhr) {
                    notify('error', xhr.responseJSON?.message || 'No fue posible consultar.');
                });
        });

        $('#tablaAfiliados').on('click', '.btn-eliminar', function () {
            const id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Eliminar afiliado',
                text: 'Esta accion no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: endpoints.eliminar,
                    method: 'POST',
                    data: { id },
                    dataType: 'json',
                })
                    .done(function (response) {
                        notify('success', response.message || 'Afiliado eliminado.');
                        recargarTablaAfiliados();
                    })
                    .fail(function (xhr) {
                        notify('error', xhr.responseJSON?.message || 'No fue posible eliminar.');
                    });
            });
        });

        $('#buscarAfiliado').on('input', function () {
            if (tablaAfiliados) {
                tablaAfiliados.search(this.value).draw();
                return;
            }

            renderAfiliados();
        });

        $('#filtroTipoIdentificacion').on('change', redibujarTablaAfiliados);
        $('#estadoAfiliacion').on('change', toggleCamposDesafiliacion);

        $('#btnLimpiarFiltros').on('click', function () {
            $('#buscarAfiliado').val('');
            if (tablaAfiliados) {
                tablaAfiliados.search('');
            }
            $('#filtroTipoIdentificacion').val('').trigger('change');
            $('#filtroFechas').val('');
            filtroFechas = { inicio: null, fin: null };
            redibujarTablaAfiliados();
        });
    }

    $(function () {
        initPlugins();
        registrarEventos();
        cargarAfiliados();
        cargarAsambleas();
    });
})(jQuery);

let tablaAsistenciaAsamblea = null;
const asistentesSeleccionados = new Set();

function escapeHtmlAsamblea(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function cargarResumenQuorum() {
    $.ajax({
        url: 'index.php?ruta=asambleaResumenQuorum',
        type: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (!resp.ok) return;

            $('#totalActivos').text(resp.data.total_activos);
            $('#mitadMasUno').text(resp.data.mitad_mas_uno);
            $('#treintaPorCiento').text(resp.data.treinta_por_ciento);
            $('#veintePorCiento').text(resp.data.veinte_por_ciento);
        }
    });
}

function actualizarAsistentesSeleccionados() {
    $('#afiliados_asistentes').val(Array.from(asistentesSeleccionados).join(','));
}

if ($('#tablaAsistenciaAsamblea').length) {
    cargarResumenQuorum();

    tablaAsistenciaAsamblea = $('#tablaAsistenciaAsamblea').DataTable({
        ajax: 'index.php?ruta=asambleaAfiliadosActivos',
        responsive: true,
        autoWidth: false,
        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function (data) {
                    const checked = asistentesSeleccionados.has(String(data)) ? 'checked' : '';

                    return `
                        <div class="icheck-primary text-center">
                            <input type="checkbox" class="check-asistencia" value="${data}" ${checked}>
                        </div>
                    `;
                }
            },
            { data: 'numero_afiliado' },
            { data: 'nombres_completos' },
            { data: 'numero_identificacion' },
            { data: 'telefono' },
            { data: 'comite_trabajo' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });

    tablaAsistenciaAsamblea.on('draw', function () {
        $('.check-asistencia').each(function () {
            $(this).prop('checked', asistentesSeleccionados.has(String($(this).val())));
        });
    });

    $(document).on('change', '.check-asistencia', function () {
        const id = String($(this).val());
        if (this.checked) {
            asistentesSeleccionados.add(id);
        } else {
            asistentesSeleccionados.delete(id);
        }

        actualizarAsistentesSeleccionados();
    });
}

function limpiarAsambleaCard() {
    $('#formAsamblea')[0].reset();
    $('#id').val('');
    $('#afiliados_asistentes').val('');
    $('#tituloFormularioAsamblea').text('Registrar acta de asamblea');
    asistentesSeleccionados.clear();

    inicializarFechaAsamblea();
    $('#fecha_asamblea').val(moment().format('YYYY-MM-DD'));
    if ($('#fecha_asamblea').data('daterangepicker')) {
        $('#fecha_asamblea').data('daterangepicker').setStartDate(moment());
        $('#fecha_asamblea').data('daterangepicker').setEndDate(moment());
    }

    if (tablaAsistenciaAsamblea) {
        tablaAsistenciaAsamblea.draw(false);
    }
}

function mostrarFormularioAsamblea() {
    $('#cardFormularioAsamblea').removeClass('d-none');
    $('html, body').animate({
        scrollTop: $('#cardFormularioAsamblea').offset().top - 70
    }, 400);
}

function cargarAsambleaEnFormulario(data) {
    $('#id').val(data.id);
    $('#numero_acta').val(data.numero_acta);
    $('#fecha_asamblea').val(data.fecha_asamblea);
    $('#observaciones').val(data.observaciones || '');
    $('#tituloFormularioAsamblea').text('Editar acta de asamblea');

    inicializarFechaAsamblea();
    if ($('#fecha_asamblea').data('daterangepicker')) {
        $('#fecha_asamblea').data('daterangepicker').setStartDate(moment(data.fecha_asamblea));
        $('#fecha_asamblea').data('daterangepicker').setEndDate(moment(data.fecha_asamblea));
    }

    asistentesSeleccionados.clear();
    (data.afiliados || []).forEach(function (id) {
        asistentesSeleccionados.add(String(id));
    });

    actualizarAsistentesSeleccionados();

    if (tablaAsistenciaAsamblea) {
        tablaAsistenciaAsamblea.draw(false);
    }
}

function recargarTablaActas() {
    if ($.fn.dataTable && $.fn.dataTable.isDataTable('#tablaAsambleas')) {
        $('#tablaAsambleas').DataTable().ajax.reload(null, false);
    }
}

function obtenerFilasAsistencia() {
    if (!tablaAsistenciaAsamblea) {
        return [];
    }

    return tablaAsistenciaAsamblea.rows().data().toArray();
}

function renderListaAsistencia(titulo, filas, claseBadge) {
    const items = filas.length
        ? filas.map(function (item) {
            return `
                <tr>
                    <td><span class="badge ${claseBadge}">${titulo}</span></td>
                    <td>${escapeHtmlAsamblea(item.numero_afiliado || '')}</td>
                    <td>${escapeHtmlAsamblea(item.nombres_completos || '')}</td>
                    <td>${escapeHtmlAsamblea(item.numero_identificacion || '')}</td>
                    <td>${escapeHtmlAsamblea(item.telefono || '-')}</td>
                    <td>${escapeHtmlAsamblea(item.comite_trabajo || '-')}</td>
                </tr>
            `;
        }).join('')
        : '<tr><td colspan="6" class="text-center text-muted">Sin registros.</td></tr>';

    return `
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-hover mb-0">
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Nro. Afiliado</th>
                        <th>Nombre</th>
                        <th>Identificacion</th>
                        <th>Telefono</th>
                        <th>Comite</th>
                    </tr>
                </thead>
                <tbody>${items}</tbody>
            </table>
        </div>
    `;
}

function mostrarResultadoAsamblea(data) {
    const asistentes = new Set((data.afiliados || []).map(String));
    const filas = obtenerFilasAsistencia();
    const presentes = filas.filter(function (item) {
        return asistentes.has(String(item.id));
    });
    const ausentes = filas.filter(function (item) {
        return !asistentes.has(String(item.id));
    });

    $('#detalleResultadoAsamblea').html(`
        <div class="mb-3">
            <h4 class="mb-1">Acta ${escapeHtmlAsamblea(data.numero_acta || '')}</h4>
            <span class="text-muted">Fecha: ${escapeHtmlAsamblea(data.fecha_asamblea || '')}</span>
        </div>
        <div class="row">
            <div class="col-12 col-xl-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-success">
                        <h3 class="card-title">Registros de asistencia del acta</h3>
                    </div>
                    <div class="card-body p-0">
                        ${renderListaAsistencia('Presente', presentes, 'badge-success')}
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-danger">
                        <h3 class="card-title">Ausentes</h3>
                    </div>
                    <div class="card-body p-0">
                        ${renderListaAsistencia('Ausente', ausentes, 'badge-danger')}
                    </div>
                </div>
            </div>
        </div>
    `);

    $('#cardResultadoAsamblea').removeClass('d-none');
    $('html, body').animate({
        scrollTop: $('#cardResultadoAsamblea').offset().top - 70
    }, 400);
}

$('#btnNuevaAsamblea').on('click', function () {
    limpiarAsambleaCard();
    mostrarFormularioAsamblea();
});

$('#btnCancelarAsamblea').on('click', function () {
    $('#cardFormularioAsamblea').addClass('d-none');
});

$('#formAsamblea').on('submit', function (event) {
    event.preventDefault();
    actualizarAsistentesSeleccionados();

    const $button = $('#btnGuardarAsamblea');
    $button.prop('disabled', true);

    $.ajax({
        url: 'index.php?ruta=asambleaGuardar',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
    })
        .done(function (response) {
            Swal.fire({
                icon: 'success',
                title: response.message || 'Acta guardada.',
                timer: 1800,
                showConfirmButton: false,
            });
            $('#cardFormularioAsamblea').addClass('d-none');
            recargarTablaActas();
            cargarResumenQuorum();
        })
        .fail(function (xhr) {
            Swal.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'No fue posible guardar el acta.',
            });
        })
        .always(function () {
            $button.prop('disabled', false);
        });
});

$('#tablaAsambleas').on('click', '.btn-editar-asamblea', function () {
    const id = $(this).data('id');

    $.getJSON(`index.php?ruta=asambleaEditar&id=${id}`)
        .done(function (response) {
            if (!response.ok) {
                Swal.fire({ icon: 'error', title: response.message || 'Acta no encontrada.' });
                return;
            }

            cargarAsambleaEnFormulario(response.data);
            mostrarFormularioAsamblea();
        })
        .fail(function (xhr) {
            Swal.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'No fue posible consultar el acta.',
            });
        });
});

$('#tablaAsambleas').on('click', '.btn-ver-asamblea', function () {
    const id = $(this).data('id');

    $.getJSON(`index.php?ruta=asambleaEditar&id=${id}`)
        .done(function (response) {
            if (!response.ok) {
                Swal.fire({ icon: 'error', title: response.message || 'Acta no encontrada.' });
                return;
            }

            mostrarResultadoAsamblea(response.data);
        })
        .fail(function (xhr) {
            Swal.fire({
                icon: 'error',
                title: xhr.responseJSON?.message || 'No fue posible consultar la asistencia.',
            });
        });
});

function inicializarFechaAsamblea() {
    if ($('#fecha_asamblea').length && !$('#fecha_asamblea').data('daterangepicker')) {
        $('#fecha_asamblea').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: true,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                firstDay: 1
            }
        });
    }
}

let tablaComunicaciones = null;
let filtroComunicacionesRegistrado = false;

function escapeHtmlComunicacion(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function etiquetaTipoComunicacion(tipo) {
    if (tipo === 'recibida') {
        return '<span class="badge badge-info">Recibida</span>';
    }

    return '<span class="badge badge-primary">Enviada</span>';
}

function etiquetaMedioComunicacion(medio) {
    const labels = {
        pagina_web: 'Pagina web',
        correo: 'Correo',
        presencial: 'Presencial',
        otro: 'Otro',
    };

    return labels[medio] || medio || '-';
}

function renderLinkDrive(url, texto) {
    if (!url) {
        return '<span class="text-muted">-</span>';
    }

    return `
        <a class="btn btn-outline-primary btn-sm" href="${escapeHtmlComunicacion(url)}" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt"></i>
            ${texto}
        </a>
    `;
}

function renderEstadoRespuesta(row) {
    if (row.tiene_respuesta) {
        const link = row.url_drive_respuesta
            ? `<div class="mt-1">${renderLinkDrive(row.url_drive_respuesta, 'Ver respuesta')}</div>`
            : '';

        return `<span class="badge badge-success">Respondida</span>${link}`;
    }

    return '<span class="badge badge-warning">Sin respuesta</span>';
}

function renderDiasHabilesComunicacion(row) {
    const dias = Number(row.dias_habiles_transcurridos || 0);
    const clase = row.tiene_respuesta ? 'badge-secondary' : (dias >= 15 ? 'badge-danger' : 'badge-warning');

    return `<span class="badge ${clase}">${dias}</span>`;
}

function registrarFiltroComunicaciones() {
    if (!$.fn.dataTable || filtroComunicacionesRegistrado) {
        return;
    }

    $.fn.dataTable.ext.search.push(function (settings, _data, dataIndex, rowData) {
        if (settings.nTable.id !== 'tablaComunicaciones') {
            return true;
        }

        const item = rowData || (tablaComunicaciones ? tablaComunicaciones.row(dataIndex).data() : null);
        if (!item) {
            return true;
        }

        const tipo = $('#filtroTipoComunicacion').val();
        const medio = $('#filtroMedioComunicacion').val();
        const respuesta = $('#filtroRespuestaComunicacion').val();

        if (tipo && item.tipo_comunicacion !== tipo) {
            return false;
        }

        if (medio && item.medio_radicacion !== medio) {
            return false;
        }

        if (respuesta === 'sin_respuesta' && item.tiene_respuesta) {
            return false;
        }

        if (respuesta === 'respondidas' && !item.tiene_respuesta) {
            return false;
        }

        return true;
    });

    filtroComunicacionesRegistrado = true;
}

function inicializarFechasComunicacion() {
    if ($('#fechaRadicadoComunicacion').length && !$('#fechaRadicadoComunicacion').data('daterangepicker')) {
        $('#fechaRadicadoComunicacion').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: true,
            startDate: moment(),
            minYear: 1900,
            maxYear: parseInt(moment().format('YYYY'), 10) + 10,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
            },
        });
    }

    if ($('#fechaRespuestaComunicacion').length && !$('#fechaRespuestaComunicacion').data('daterangepicker')) {
        $('#fechaRespuestaComunicacion').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            startDate: moment(),
            minYear: 1900,
            maxYear: parseInt(moment().format('YYYY'), 10) + 10,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: 'Limpiar',
            },
        });

        $('#fechaRespuestaComunicacion').on('apply.daterangepicker', function (_event, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('#fechaRespuestaComunicacion').on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    }
}

function inicializarDataTableComunicaciones() {
    if (!$('#tablaComunicaciones').length || !$.fn.DataTable) {
        return false;
    }

    if ($.fn.dataTable.isDataTable('#tablaComunicaciones')) {
        tablaComunicaciones.ajax.reload(null, false);
        return true;
    }

    registrarFiltroComunicaciones();

    tablaComunicaciones = $('#tablaComunicaciones').DataTable({
        ajax: {
            url: 'index.php?ruta=comunicacionListar',
            dataSrc: 'data',
        },
        columns: [
            {
                data: 'tipo_comunicacion',
                render: function (data, type) {
                    return type === 'display' ? etiquetaTipoComunicacion(data) : data;
                },
            },
            {
                data: 'asunto',
                render: function (data) {
                    return escapeHtmlComunicacion(data);
                },
            },
            {
                data: 'tercero',
                render: function (data) {
                    return escapeHtmlComunicacion(data);
                },
            },
            {
                data: 'numero_radicado',
                render: function (data) {
                    return escapeHtmlComunicacion(data || '-');
                },
            },
            {
                data: null,
                render: function (_data, _type, row) {
                    return escapeHtmlComunicacion(row.fecha_radicado_texto || row.fecha_radicado || '-');
                },
            },
            {
                data: 'medio_radicacion',
                render: function (data) {
                    return escapeHtmlComunicacion(etiquetaMedioComunicacion(data));
                },
            },
            {
                data: 'url_drive_comunicacion',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return renderLinkDrive(data, 'Ver');
                },
            },
            {
                data: null,
                render: function (_data, _type, row) {
                    const texto = row.seguimiento ? escapeHtmlComunicacion(row.seguimiento) : '<span class="text-muted">-</span>';
                    const link = row.url_drive_seguimiento ? `<div class="mt-1">${renderLinkDrive(row.url_drive_seguimiento, 'Soporte')}</div>` : '';

                    return `${texto}${link}`;
                },
            },
            {
                data: null,
                render: function (_data, type, row) {
                    if (type !== 'display') {
                        return row.tiene_respuesta ? 'Respondida' : 'Sin respuesta';
                    }

                    return renderEstadoRespuesta(row);
                },
            },
            {
                data: 'dias_habiles_transcurridos',
                className: 'text-center',
                render: function (_data, type, row) {
                    return type === 'display' ? renderDiasHabilesComunicacion(row) : Number(row.dias_habiles_transcurridos || 0);
                },
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                className: 'text-right',
                render: function (id) {
                    return `
                        <span class="row-actions">
                            <button type="button" class="btn btn-outline-primary btn-editar-comunicacion" data-id="${id}" title="Editar" aria-label="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </span>
                    `;
                },
            },
        ],
        deferRender: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[4, 'desc']],
        language: {
            decimal: '',
            emptyTable: 'No hay comunicaciones registradas.',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ comunicaciones',
            infoEmpty: 'Mostrando 0 comunicaciones',
            infoFiltered: '(filtrado de _MAX_ comunicaciones)',
            lengthMenu: 'Mostrar _MENU_ registros',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            search: 'Buscar:',
            zeroRecords: 'No se encontraron comunicaciones.',
            paginate: {
                first: 'Primero',
                last: 'Ultimo',
                next: 'Siguiente',
                previous: 'Anterior',
            },
        },
    });

    return true;
}

function limpiarFormularioComunicacion() {
    const form = document.getElementById('formComunicacion');
    if (!form) {
        return;
    }

    form.reset();
    $('#comunicacionId').val('');
    $('#tituloFormularioComunicacion').text('Registrar comunicacion');
    inicializarFechasComunicacion();
    $('#fechaRadicadoComunicacion').val(moment().format('YYYY-MM-DD'));
    $('#fechaRespuestaComunicacion').val('');

    if ($('#fechaRadicadoComunicacion').data('daterangepicker')) {
        $('#fechaRadicadoComunicacion').data('daterangepicker').setStartDate(moment());
        $('#fechaRadicadoComunicacion').data('daterangepicker').setEndDate(moment());
    }
}

function mostrarFormularioComunicacion() {
    $('#cardFormularioComunicacion').removeClass('d-none');
    $('html, body').animate({
        scrollTop: $('#cardFormularioComunicacion').offset().top - 70,
    }, 400);
}

function cargarComunicacionEnFormulario(data) {
    $('#comunicacionId').val(data.id);
    $('#tipoComunicacion').val(data.tipo_comunicacion);
    $('#asuntoComunicacion').val(data.asunto);
    $('#terceroComunicacion').val(data.tercero);
    $('#fechaRadicadoComunicacion').val(data.fecha_radicado);
    $('#numeroRadicadoComunicacion').val(data.numero_radicado || '');
    $('#medioRadicacionComunicacion').val(data.medio_radicacion);
    $('#urlDriveComunicacion').val(data.url_drive_comunicacion);
    $('#seguimientoComunicacion').val(data.seguimiento || '');
    $('#urlDriveSeguimientoComunicacion').val(data.url_drive_seguimiento || '');
    $('#fechaRespuestaComunicacion').val(data.fecha_respuesta || '');
    $('#respuestaComunicacion').val(data.respuesta || '');
    $('#urlDriveRespuestaComunicacion').val(data.url_drive_respuesta || '');
    $('#observacionesComunicacion').val(data.observaciones || '');
    $('#tituloFormularioComunicacion').text('Editar comunicacion');

    inicializarFechasComunicacion();
    if ($('#fechaRadicadoComunicacion').data('daterangepicker')) {
        $('#fechaRadicadoComunicacion').data('daterangepicker').setStartDate(moment(data.fecha_radicado));
        $('#fechaRadicadoComunicacion').data('daterangepicker').setEndDate(moment(data.fecha_radicado));
    }
    if (data.fecha_respuesta && $('#fechaRespuestaComunicacion').data('daterangepicker')) {
        $('#fechaRespuestaComunicacion').data('daterangepicker').setStartDate(moment(data.fecha_respuesta));
        $('#fechaRespuestaComunicacion').data('daterangepicker').setEndDate(moment(data.fecha_respuesta));
    }
}

function recargarTablaComunicaciones() {
    if (tablaComunicaciones) {
        tablaComunicaciones.ajax.reload(null, false);
    }
}

$(function () {
    if (!$('#tablaComunicaciones').length) {
        return;
    }

    inicializarFechasComunicacion();
    inicializarDataTableComunicaciones();

    $('#btnNuevaComunicacion').on('click', function () {
        limpiarFormularioComunicacion();
        mostrarFormularioComunicacion();
    });

    $('#btnCancelarComunicacion').on('click', function () {
        $('#cardFormularioComunicacion').addClass('d-none');
    });

    $('#formComunicacion').on('submit', function (event) {
        event.preventDefault();

        const $button = $('#btnGuardarComunicacion');
        $button.prop('disabled', true);

        $.ajax({
            url: 'index.php?ruta=comunicacionGuardar',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
        })
            .done(function (response) {
                Swal.fire({
                    icon: 'success',
                    title: response.message || 'Comunicacion guardada.',
                    timer: 1800,
                    showConfirmButton: false,
                });
                $('#cardFormularioComunicacion').addClass('d-none');
                recargarTablaComunicaciones();
            })
            .fail(function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: xhr.responseJSON?.message || 'No fue posible guardar la comunicacion.',
                });
            })
            .always(function () {
                $button.prop('disabled', false);
            });
    });

    $('#tablaComunicaciones').on('click', '.btn-editar-comunicacion', function () {
        const id = $(this).data('id');

        $.getJSON(`index.php?ruta=comunicacionEditar&id=${id}`)
            .done(function (response) {
                if (!response.ok) {
                    Swal.fire({ icon: 'error', title: response.message || 'Comunicacion no encontrada.' });
                    return;
                }

                cargarComunicacionEnFormulario(response.data);
                mostrarFormularioComunicacion();
            })
            .fail(function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: xhr.responseJSON?.message || 'No fue posible consultar la comunicacion.',
                });
            });
    });

    $('#filtroTipoComunicacion, #filtroMedioComunicacion, #filtroRespuestaComunicacion').on('change', function () {
        if (tablaComunicaciones) {
            tablaComunicaciones.draw();
        }
    });

    $('#btnLimpiarFiltrosComunicacion').on('click', function () {
        $('#filtroTipoComunicacion').val('');
        $('#filtroMedioComunicacion').val('');
        $('#filtroRespuestaComunicacion').val('');
        if (tablaComunicaciones) {
            tablaComunicaciones.search('').draw();
        }
    });
});

(function ($) {
    'use strict';

    function inicializarSelectOrganizacion() {
        if (!$.fn.select2) {
            return;
        }

        $('.js-select2-organizacion').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: function () {
                return $(this).data('placeholder') || 'Seleccione';
            },
        });
    }

    function inicializarFechaOrganizacion(selector, requerida) {
        const $input = $(selector);

        if (!$input.length || !$.fn.daterangepicker || $input.data('daterangepicker')) {
            return;
        }

        const valorInicial = $input.val();
        const fechaInicial = valorInicial ? moment(valorInicial, 'YYYY-MM-DD') : moment();
        const startDate = fechaInicial.isValid() ? fechaInicial : moment();

        $input.daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: requerida || Boolean(valorInicial),
            startDate,
            minYear: 1900,
            maxYear: parseInt(moment().format('YYYY'), 10) + 10,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: requerida ? 'Cancelar' : 'Limpiar',
            },
        });

        if (requerida && !valorInicial) {
            $input.val(startDate.format('YYYY-MM-DD'));
        }

        if (!requerida && !valorInicial) {
            $input.val('');
        }

        $input.on('apply.daterangepicker', function (_event, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        if (!requerida) {
            $input.on('cancel.daterangepicker', function () {
                $(this).val('');
            });
        }
    }

    function inicializarFechasOrganizacion() {
        inicializarFechaOrganizacion('#periodoInicioOrganizacion', false);
        inicializarFechaOrganizacion('#periodoFinOrganizacion', false);
        inicializarFechaOrganizacion('#fechaResolucionDignatarios', true);
    }

    function sincronizarFechaOrganizacion(selector, requerida) {
        const $input = $(selector);
        const picker = $input.data('daterangepicker');

        if (!$input.length || !picker) {
            return;
        }

        const valor = $input.val();
        if (valor) {
            const fecha = moment(valor, 'YYYY-MM-DD');
            if (fecha.isValid()) {
                picker.setStartDate(fecha);
                picker.setEndDate(fecha);
            }
            return;
        }

        if (requerida) {
            const hoy = moment();
            picker.setStartDate(hoy);
            picker.setEndDate(hoy);
            $input.val(hoy.format('YYYY-MM-DD'));
        }
    }

    function toggleBotonesOrganizacion(loading) {
        $('#formOrganizacion button[type="submit"]').each(function () {
            const $button = $(this);
            $button.prop('disabled', loading);
            $button.data('original-text', $button.data('original-text') || $button.html());
            $button.html(loading ? '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Guardando' : $button.data('original-text'));
        });
    }

    $(function () {
        if (!$('#formOrganizacion').length) {
            return;
        }

        inicializarSelectOrganizacion();
        inicializarFechasOrganizacion();

        $('#formOrganizacion').on('submit', function (event) {
            event.preventDefault();

            toggleBotonesOrganizacion(true);

            $.ajax({
                url: 'index.php?ruta=organizacionGuardar',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
            })
                .done(function (response) {
                    if (response.id) {
                        $('#organizacionId').val(response.id);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: response.message || 'Organizacion guardada.',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                })
                .fail(function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: xhr.responseJSON?.message || 'No fue posible guardar la organizacion.',
                    });
                })
                .always(function () {
                    toggleBotonesOrganizacion(false);
                });
        });

        $('#btnRestablecerOrganizacion').on('click', function () {
            setTimeout(function () {
                $('.js-select2-organizacion').trigger('change');
                sincronizarFechaOrganizacion('#periodoInicioOrganizacion', false);
                sincronizarFechaOrganizacion('#periodoFinOrganizacion', false);
                sincronizarFechaOrganizacion('#fechaResolucionDignatarios', true);
            }, 0);
        });
    });
})(jQuery);

let tablaMovimientosTesoreria = null;

function formatoMoneda(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(valor || 0);
}

function inicializarFechaTesoreria() {
    if ($('#tesoreria_fecha').length && !$('#tesoreria_fecha').data('daterangepicker')) {
        $('#tesoreria_fecha').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: true,
            locale: {
                format: 'YYYY-MM-DD',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: [
                    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                ],
                firstDay: 1
            }
        });
    }
}

function cargarCategoriasTesoreria(tipoMovimiento, categoriaSeleccionada = '') {
    const $categoria = $('#tesoreria_categoria_id');

    $categoria.html('<option value="">Cargando categorias...</option>');

    if (!tipoMovimiento) {
        $categoria.html('<option value="">Seleccione primero el tipo de movimiento...</option>');
        return;
    }

    $.ajax({
        url: 'index.php?ruta=tesoreriaCategoriasPorTipo',
        type: 'GET',
        dataType: 'json',
        data: {
            tipo_movimiento: tipoMovimiento
        },
        success: function (resp) {
            if (!resp.ok) {
                Swal.fire('Atención', resp.message, 'warning');
                return;
            }

            let options = '<option value="">Seleccione...</option>';

            resp.data.forEach(function (categoria) {
                const selected = String(categoria.id) === String(categoriaSeleccionada) ? 'selected' : '';

                options += `
                    <option value="${categoria.id}" ${selected}>
                        ${categoria.nombre}
                    </option>
                `;
            });

            $categoria.html(options).trigger('change');
        }
    });
}

function cargarResumenTesoreria() {
    $.ajax({
        url: 'index.php?ruta=tesoreriaResumen',
        type: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (!resp.ok) return;

            $('#totalEntradasTesoreria').text(formatoMoneda(resp.data.total_entradas));
            $('#totalSalidasTesoreria').text(formatoMoneda(resp.data.total_salidas));
            $('#saldoTesoreria').text(formatoMoneda(resp.data.saldo));
        }
    });
}

if ($('#tablaMovimientosTesoreria').length) {
    inicializarFechaTesoreria();
    cargarResumenTesoreria();

    tablaMovimientosTesoreria = $('#tablaMovimientosTesoreria').DataTable({
        ajax: 'index.php?ruta=tesoreriaListar',
        responsive: true,
        autoWidth: false,
        columns: [
            { data: 'fecha' },
            {
                data: 'tipo_movimiento',
                render: function (data) {
                    const badge = data === 'entrada' ? 'success' : 'danger';
                    const texto = data === 'entrada' ? 'Entrada' : 'Salida';

                    return `<span class="badge badge-${badge}">${texto}</span>`;
                }
            },
            { data: 'categoria' },
            { data: 'concepto' },
            { data: 'medio_pago' },
            { data: 'numero_soporte' },
            {
                data: 'valor',
                className: 'text-right',
                render: function (data) {
                    return formatoMoneda(data);
                }
            },
            {
                data: 'id',
                className: 'text-right',
                orderable: false,
                searchable: false,
                render: function (data) {
                    return `
                        <div class="row-actions">
                            <button type="button"
                                    class="btn btn-sm btn-info btnEditarMovimientoTesoreria"
                                    data-id="${data}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-danger btnEliminarMovimientoTesoreria"
                                    data-id="${data}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        }
    });
}

$('#btnNuevoMovimientoTesoreria').on('click', function () {
    $('#formMovimientoTesoreria')[0].reset();
    $('#tesoreria_id').val('');
    $('#tesoreria_categoria_id').html('<option value="">Seleccione primero el tipo de movimiento...</option>');

    $('#cardFormularioMovimientoTesoreria').removeClass('d-none');

    inicializarFechaTesoreria();

    $('html, body').animate({
        scrollTop: $('#cardFormularioMovimientoTesoreria').offset().top - 70
    }, 400);
});

$('#btnCancelarMovimientoTesoreria').on('click', function () {
    $('#cardFormularioMovimientoTesoreria').addClass('d-none');
});

$('#tesoreria_tipo_movimiento').on('change', function () {
    cargarCategoriasTesoreria($(this).val());
});

$('#formMovimientoTesoreria').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: 'index.php?ruta=tesoreriaGuardar',
        type: 'POST',
        dataType: 'json',
        data: $(this).serialize(),
        success: function (resp) {
            if (!resp.ok) {
                Swal.fire('Atención', resp.message, 'warning');
                return;
            }

            Swal.fire('Correcto', resp.message, 'success');

            $('#cardFormularioMovimientoTesoreria').addClass('d-none');

            tablaMovimientosTesoreria.ajax.reload(null, false);
            cargarResumenTesoreria();
        },
        error: function () {
            Swal.fire('Error', 'No fue posible guardar el movimiento.', 'error');
        }
    });
});

$(document).on('click', '.btnEditarMovimientoTesoreria', function () {
    const id = $(this).data('id');

    $.ajax({
        url: 'index.php?ruta=tesoreriaEditar',
        type: 'GET',
        dataType: 'json',
        data: { id: id },
        success: function (resp) {
            if (!resp.ok) {
                Swal.fire('Atención', resp.message, 'warning');
                return;
            }

            const data = resp.data;

            $('#tesoreria_id').val(data.id);
            $('#tesoreria_fecha').val(data.fecha);
            $('#tesoreria_tipo_movimiento').val(data.tipo_movimiento).trigger('change');
            $('#tesoreria_concepto').val(data.concepto);
            $('#tesoreria_valor').val(data.valor);
            $('#tesoreria_medio_pago').val(data.medio_pago).trigger('change');
            $('#tesoreria_numero_soporte').val(data.numero_soporte);
            $('#tesoreria_observaciones').val(data.observaciones);

            cargarCategoriasTesoreria(data.tipo_movimiento, data.categoria_id);

            $('#cardFormularioMovimientoTesoreria').removeClass('d-none');

            inicializarFechaTesoreria();

            $('html, body').animate({
                scrollTop: $('#cardFormularioMovimientoTesoreria').offset().top - 70
            }, 400);
        }
    });
});

$(document).on('click', '.btnEliminarMovimientoTesoreria', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Eliminar movimiento',
        text: 'Esta accion no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.ajax({
            url: 'index.php?ruta=tesoreriaEliminar',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function (resp) {
                if (!resp.ok) {
                    Swal.fire('Atención', resp.message, 'warning');
                    return;
                }

                Swal.fire('Correcto', resp.message, 'success');

                tablaMovimientosTesoreria.ajax.reload(null, false);
                cargarResumenTesoreria();
            },
            error: function () {
                Swal.fire('Error', 'No fue posible eliminar el movimiento.', 'error');
            }
        });
    });
});
