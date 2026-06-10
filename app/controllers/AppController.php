<?php

declare(strict_types=1);

class AppController
{
    private Usuario $usuarios;
    private Afiliado $afiliados;
    private Asamblea $asambleas;
    private Comunicacion $comunicaciones;
    private Organizacion $organizacion;

    private Tesoreria $tesoreria;

    public function __construct()
    {
        $this->usuarios = new Usuario();
        $this->afiliados = new Afiliado();
        $this->asambleas = new Asamblea();
        $this->comunicaciones = new Comunicacion();
        $this->organizacion = new Organizacion();
        $this->tesoreria = new Tesoreria();
    }

    public function dispatch(): void
    {
        try {
            $ruta = $_GET['ruta'] ?? 'dashboard';

            switch ($ruta) {
                case 'login':
                    $this->login();
                    break;

                case 'loginValidar':
                    $this->loginValidar();
                    break;

                case 'logout':
                    $this->logout();
                    break;

                case 'dashboard':
                    $this->dashboard();
                    break;

                case 'afiliados':
                    $this->afiliados();
                    break;

                case 'afiliadoListar':
                    $this->afiliadoListar();
                    break;

                case 'afiliadoGuardar':
                    $this->afiliadoGuardar();
                    break;

                case 'afiliadoEditar':
                    $this->afiliadoEditar();
                    break;

                case 'afiliadoEliminar':
                    $this->afiliadoEliminar();
                    break;

                case 'asambleas':
                    $this->asambleas();
                    break;

                case 'asambleaListar':
                    $this->asambleaListar();
                    break;

                case 'asambleaGuardar':
                    $this->asambleaGuardar();
                    break;

                case 'asambleaEditar':
                    $this->asambleaEditar();
                    break;

                case 'asambleaEliminar':
                    $this->asambleaEliminar();
                    break;

                case 'asambleaAfiliadosActivos':
                    $this->asambleaAfiliadosActivos();
                    break;

                case 'asambleaResumenQuorum':
                    $this->asambleaResumenQuorum();
                    break;

                case 'comunicaciones':
                    $this->comunicaciones();
                    break;

                case 'comunicacionListar':
                    $this->comunicacionListar();
                    break;

                case 'comunicacionGuardar':
                    $this->comunicacionGuardar();
                    break;

                case 'comunicacionEditar':
                    $this->comunicacionEditar();
                    break;

                case 'organizacion':
                    $this->organizacion();
                    break;

                case 'organizacionObtener':
                    $this->organizacionObtener();
                    break;

                case 'organizacionGuardar':
                    $this->organizacionGuardar();
                    break;

                case 'tesoreria':
                    $this->tesoreria();
                    break;

                case 'tesoreriaListar':
                    $this->tesoreriaListar();
                    break;

                case 'tesoreriaGuardar':
                    $this->tesoreriaGuardar();
                    break;

                case 'tesoreriaEditar':
                    $this->tesoreriaEditar();
                    break;

                case 'tesoreriaEliminar':
                    $this->tesoreriaEliminar();
                    break;

                case 'tesoreriaCategoriasPorTipo':
                    $this->tesoreriaCategoriasPorTipo();
                    break;

                case 'tesoreriaResumen':
                    $this->tesoreriaResumen();
                    break;

                default:
                    $this->redirect('dashboard');
                    break;
            }
        } catch (Throwable $e) {
            $this->responderError($e);
        }
    }

    private function login(): void
    {
        if ($this->estaAutenticado()) {
            $this->redirect('dashboard');
        }

        require APP_PATH . '/views/login.php';
    }

    private function loginValidar(): void
    {
        $this->soloPost();

        $email = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->json(['ok' => false, 'message' => 'Ingrese email y contrasena.'], 422);
        }

        $usuario = $this->usuarios->buscarPorEmail($email);

        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            $this->json(['ok' => false, 'message' => 'Credenciales incorrectas.'], 401);
        }

        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nombres' => $usuario['nombres'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol'],
        ];

        $this->json([
            'ok' => true,
            'message' => 'Sesion iniciada.',
            'redirect' => BASE_URL . 'index.php?ruta=dashboard',
        ]);
    }

    private function logout(): void
    {
        session_destroy();
        $this->redirect('login');
    }

    private function dashboard(): void
    {
        $this->requiereLogin();

        $this->render('dashboard', [
            'titulo' => 'Panel principal',
            'totalAfiliados' => $this->afiliados->contar(),
        ]);
    }

    private function afiliados(): void
    {
        $this->requiereLogin();

        $this->render('afiliados/index', [
            'titulo' => 'Afiliados',
            'tiposIdentificacion' => $this->tiposIdentificacion(),
            'comites' => $this->comitesTrabajo(),
        ]);
    }

    private function afiliadoListar(): void
    {
        $this->requiereLogin();
        $this->json(['data' => $this->afiliados->listar()]);
    }

    private function afiliadoEditar(): void
    {
        $this->requiereLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $afiliado = $this->afiliados->obtener($id);

        if (!$afiliado) {
            $this->json(['ok' => false, 'message' => 'Afiliado no encontrado.'], 404);
        }

        $this->json(['ok' => true, 'data' => $afiliado]);
    }

    private function afiliadoGuardar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validarAfiliado();

        try {
            if ($id > 0) {
                $this->afiliados->actualizar($id, $data);
                $this->json(['ok' => true, 'message' => 'Afiliado actualizado correctamente.']);
            }

            $nuevoId = $this->afiliados->crear($data);
            $this->json(['ok' => true, 'message' => 'Afiliado registrado correctamente.', 'id' => $nuevoId]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                $this->json([
                    'ok' => false,
                    'message' => 'El numero de afiliado o identificacion ya existe.',
                ], 409);
            }

            throw $e;
        }
    }

    private function afiliadoEliminar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->json(['ok' => false, 'message' => 'Afiliado invalido.'], 422);
        }

        $this->afiliados->eliminar($id);
        $this->json(['ok' => true, 'message' => 'Afiliado eliminado correctamente.']);
    }

    private function asambleas(): void
    {
        $this->requiereLogin();

        $this->render('asambleas/index', [
            'titulo' => 'Asambleas',
            'afiliadosAsistencia' => $this->afiliados->listarOpcionesAsistencia(),
        ]);
    }

    private function asambleaListar(): void
    {
        $this->requiereLogin();
        $this->json(['data' => $this->asambleas->listar()]);
    }

    private function asambleaEditar(): void
    {
        $this->requiereLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $acta = $this->asambleas->obtener($id);

        if (!$acta) {
            $this->json(['ok' => false, 'message' => 'Acta de asamblea no encontrada.'], 404);
        }

        $this->json(['ok' => true, 'data' => $acta]);
    }

    private function asambleaGuardar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);
        [$data, $afiliadoIds] = $this->validarAsamblea();

        try {
            $actaId = $this->asambleas->guardar($data, $afiliadoIds, $id);
            $mensaje = $id > 0 ? 'Acta de asamblea actualizada correctamente.' : 'Acta de asamblea registrada correctamente.';

            $this->json(['ok' => true, 'message' => $mensaje, 'id' => $actaId]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') {
                $this->json(['ok' => false, 'message' => 'El numero de acta ya existe.'], 409);
            }

            if ($e->getCode() === '23503') {
                $this->json(['ok' => false, 'message' => 'Uno de los afiliados seleccionados no existe.'], 409);
            }

            throw $e;
        }
    }

    private function asambleaEliminar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $this->json([
            'ok' => false,
            'message' => 'Las actas de asamblea no se eliminan; solo se modifican.',
        ], 405);
    }

    private function comunicaciones(): void
    {
        $this->requiereLogin();

        $this->render('comunicaciones/index', [
            'titulo' => 'Comunicaciones',
            'tiposComunicacion' => $this->tiposComunicacion(),
            'mediosRadicacion' => $this->mediosRadicacion(),
        ]);
    }

    private function comunicacionListar(): void
    {
        $this->requiereLogin();
        $this->json(['data' => $this->comunicaciones->listar()]);
    }

    private function comunicacionEditar(): void
    {
        $this->requiereLogin();

        $id = (int) ($_GET['id'] ?? 0);
        $comunicacion = $this->comunicaciones->obtener($id);

        if (!$comunicacion) {
            $this->json(['ok' => false, 'message' => 'Comunicacion no encontrada.'], 404);
        }

        $this->json(['ok' => true, 'data' => $comunicacion]);
    }

    private function comunicacionGuardar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validarComunicacion();

        if ($id > 0) {
            $this->comunicaciones->actualizar($id, $data);
            $this->json(['ok' => true, 'message' => 'Comunicacion actualizada correctamente.']);
        }

        $nuevoId = $this->comunicaciones->crear($data);
        $this->json(['ok' => true, 'message' => 'Comunicacion registrada correctamente.', 'id' => $nuevoId]);
    }

    private function organizacion(): void
    {
        $this->requiereLogin();

        $this->render('organizacion/index', [
            'titulo' => 'Organizacion comunal',
            'organizacionActual' => $this->organizacion->obtenerActual(),
            'afiliadosDignatarios' => $this->afiliados->listarOpcionesAsistencia(),
            'bloquesDignatarios' => $this->bloquesDignatarios(),
        ]);
    }

    private function organizacionObtener(): void
    {
        $this->requiereLogin();

        $this->json([
            'ok' => true,
            'data' => $this->organizacion->obtenerActual(),
        ]);
    }

    private function organizacionGuardar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);
        [$data, $dignatarios] = $this->validarOrganizacion();

        try {
            $organizacionId = $this->organizacion->guardar($data, $dignatarios, $id);
            $this->json([
                'ok' => true,
                'message' => 'Organizacion comunal guardada correctamente.',
                'id' => $organizacionId,
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23503') {
                $this->json(['ok' => false, 'message' => 'Uno de los afiliados seleccionados no existe.'], 409);
            }

            throw $e;
        }
    }

    private function validarAfiliado(): array
    {
        $edad = filter_var($_POST['edad'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 120],
        ]);
        $mesesSancion = filter_var($_POST['meses_sancion'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 36],
        ]);

        $data = [
            'numero_afiliado' => trim($_POST['numero_afiliado'] ?? ''),
            'fecha_afiliacion' => trim($_POST['fecha_afiliacion'] ?? ''),
            'nombres_completos' => trim($_POST['nombres_completos'] ?? ''),
            'edad' => $edad,
            'numero_identificacion' => trim($_POST['numero_identificacion'] ?? ''),
            'tipo_identificacion' => trim($_POST['tipo_identificacion'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'comite_trabajo' => trim($_POST['comite_trabajo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'estado_afiliacion' => trim($_POST['estado_afiliacion'] ?? 'afiliado'),
            'acta_fallo_edicto' => trim($_POST['acta_fallo_edicto'] ?? ''),
            'meses_sancion' => $mesesSancion,
            'observaciones' => trim($_POST['observaciones'] ?? ''),
        ];

        $requeridos = [
            'numero_afiliado' => 'numero de afiliado',
            'fecha_afiliacion' => 'fecha de afiliacion',
            'nombres_completos' => 'nombres completos',
            'numero_identificacion' => 'numero de identificacion',
            'tipo_identificacion' => 'tipo de identificacion',
            'direccion' => 'direccion',
            'comite_trabajo' => 'comite de trabajo',
        ];

        foreach ($requeridos as $campo => $label) {
            if ($data[$campo] === '') {
                $this->json(['ok' => false, 'message' => "El campo {$label} es obligatorio."], 422);
            }
        }

        if ($edad === false) {
            $this->json(['ok' => false, 'message' => 'La edad debe ser un numero entre 0 y 120.'], 422);
        }

        $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_afiliacion']);
        if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_afiliacion']) {
            $this->json(['ok' => false, 'message' => 'La fecha de afiliacion debe tener formato YYYY-MM-DD.'], 422);
        }

        if (!in_array($data['estado_afiliacion'], ['afiliado', 'desafiliado'], true)) {
            $this->json(['ok' => false, 'message' => 'El estado del afiliado no es valido.'], 422);
        }

        if ($data['estado_afiliacion'] === 'desafiliado') {
            if ($data['acta_fallo_edicto'] === '') {
                $this->json(['ok' => false, 'message' => 'Debe diligenciar el acta o fallo del edicto.'], 422);
            }

            if ($mesesSancion === false) {
                $this->json(['ok' => false, 'message' => 'Los meses de sancion deben estar entre 1 y 36.'], 422);
            }
        } else {
            $data['acta_fallo_edicto'] = null;
            $data['meses_sancion'] = null;
        }

        return $data;
    }

    private function validarAsamblea(): array
    {
        $data = [
            'numero_acta' => trim($_POST['numero_acta'] ?? ''),
            'fecha_asamblea' => trim($_POST['fecha_asamblea'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
        ];

        if ($data['numero_acta'] === '') {
            $this->json(['ok' => false, 'message' => 'El numero de acta es obligatorio.'], 422);
        }

        $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha_asamblea']);
        if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha_asamblea']) {
            $this->json(['ok' => false, 'message' => 'La fecha de asamblea debe tener formato YYYY-MM-DD.'], 422);
        }

        $afiliados = $_POST['afiliados_asistentes'] ?? [];
        if (!is_array($afiliados)) {
            $afiliados = array_filter(array_map('trim', explode(',', (string) $afiliados)));
        }

        $afiliadoIds = [];
        foreach ($afiliados as $afiliadoId) {
            $afiliadoId = filter_var($afiliadoId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($afiliadoId !== false) {
                $afiliadoIds[] = (int) $afiliadoId;
            }
        }

        return [$data, array_values(array_unique($afiliadoIds))];
    }

    private function validarComunicacion(): array
    {
        $data = [
            'tipo_comunicacion' => trim($_POST['tipo_comunicacion'] ?? ''),
            'asunto' => trim($_POST['asunto'] ?? ''),
            'tercero' => trim($_POST['tercero'] ?? ''),
            'fecha_radicado' => trim($_POST['fecha_radicado'] ?? ''),
            'numero_radicado' => $this->nullSiVacio($_POST['numero_radicado'] ?? null),
            'medio_radicacion' => trim($_POST['medio_radicacion'] ?? ''),
            'url_drive_comunicacion' => trim($_POST['url_drive_comunicacion'] ?? ''),
            'seguimiento' => $this->nullSiVacio($_POST['seguimiento'] ?? null),
            'url_drive_seguimiento' => $this->nullSiVacio($_POST['url_drive_seguimiento'] ?? null),
            'fecha_respuesta' => $this->nullSiVacio($_POST['fecha_respuesta'] ?? null),
            'respuesta' => $this->nullSiVacio($_POST['respuesta'] ?? null),
            'url_drive_respuesta' => $this->nullSiVacio($_POST['url_drive_respuesta'] ?? null),
            'observaciones' => $this->nullSiVacio($_POST['observaciones'] ?? null),
        ];

        if (!array_key_exists($data['tipo_comunicacion'], $this->tiposComunicacion())) {
            $this->json(['ok' => false, 'message' => 'El tipo de comunicacion no es valido.'], 422);
        }

        if ($data['asunto'] === '') {
            $this->json(['ok' => false, 'message' => 'El asunto es obligatorio.'], 422);
        }

        if ($data['tercero'] === '') {
            $this->json(['ok' => false, 'message' => 'El destinatario o remitente es obligatorio.'], 422);
        }

        if (!array_key_exists($data['medio_radicacion'], $this->mediosRadicacion())) {
            $this->json(['ok' => false, 'message' => 'El medio de radicacion no es valido.'], 422);
        }

        if ($data['url_drive_comunicacion'] === '') {
            $this->json(['ok' => false, 'message' => 'El enlace de Google Drive de la comunicacion es obligatorio.'], 422);
        }

        $fechaRadicado = DateTime::createFromFormat('Y-m-d', $data['fecha_radicado']);
        if (!$fechaRadicado || $fechaRadicado->format('Y-m-d') !== $data['fecha_radicado']) {
            $this->json(['ok' => false, 'message' => 'La fecha de radicacion debe tener formato YYYY-MM-DD.'], 422);
        }

        if ($data['fecha_respuesta'] !== null) {
            $fechaRespuesta = DateTime::createFromFormat('Y-m-d', $data['fecha_respuesta']);
            if (!$fechaRespuesta || $fechaRespuesta->format('Y-m-d') !== $data['fecha_respuesta']) {
                $this->json(['ok' => false, 'message' => 'La fecha de respuesta debe tener formato YYYY-MM-DD.'], 422);
            }

            if ($fechaRespuesta < $fechaRadicado) {
                $this->json(['ok' => false, 'message' => 'La fecha de respuesta no puede ser anterior a la fecha de radicacion.'], 422);
            }
        }

        return $data;
    }

    private function validarOrganizacion(): array
    {
        $data = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'nit' => $this->nullSiVacio($_POST['nit'] ?? null),
            'personeria_juridica' => $this->nullSiVacio($_POST['personeria_juridica'] ?? null),
            'direccion' => $this->nullSiVacio($_POST['direccion'] ?? null),
            'barrio_vereda' => $this->nullSiVacio($_POST['barrio_vereda'] ?? null),
            'municipio' => $this->nullSiVacio($_POST['municipio'] ?? null),
            'departamento' => $this->nullSiVacio($_POST['departamento'] ?? null),
            'telefono' => $this->nullSiVacio($_POST['telefono'] ?? null),
            'email' => $this->nullSiVacio($_POST['email'] ?? null),
            'periodo_inicio' => $this->validarFechaFormulario($_POST['periodo_inicio'] ?? null, 'periodo inicio'),
            'periodo_fin' => $this->validarFechaFormulario($_POST['periodo_fin'] ?? null, 'periodo fin'),
            'numero_resolucion_dignatarios' => trim($_POST['numero_resolucion_dignatarios'] ?? ''),
            'fecha_resolucion_dignatarios' => $this->validarFechaFormulario(
                $_POST['fecha_resolucion_dignatarios'] ?? null,
                'fecha de resolucion',
                true
            ),
            'url_drive_resolucion' => $this->nullSiVacio($_POST['url_drive_resolucion'] ?? null),
            'observaciones' => $this->nullSiVacio($_POST['observaciones'] ?? null),
        ];

        if ($data['nombre'] === '') {
            $this->json(['ok' => false, 'message' => 'El nombre de la organizacion es obligatorio.'], 422);
        }

        if ($data['numero_resolucion_dignatarios'] === '') {
            $this->json(['ok' => false, 'message' => 'El numero de resolucion de dignatarios es obligatorio.'], 422);
        }

        if ($data['email'] !== null && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'message' => 'El correo electronico no es valido.'], 422);
        }

        if ($data['url_drive_resolucion'] !== null && !filter_var($data['url_drive_resolucion'], FILTER_VALIDATE_URL)) {
            $this->json(['ok' => false, 'message' => 'El enlace de Google Drive de la resolucion no es valido.'], 422);
        }

        if ($data['periodo_inicio'] !== null && $data['periodo_fin'] !== null && $data['periodo_fin'] < $data['periodo_inicio']) {
            $this->json(['ok' => false, 'message' => 'El periodo fin no puede ser anterior al periodo inicio.'], 422);
        }

        $postDignatarios = $_POST['dignatarios'] ?? [];
        if (!is_array($postDignatarios)) {
            $this->json(['ok' => false, 'message' => 'Los dignatarios seleccionados no son validos.'], 422);
        }

        $dignatarios = [];
        foreach ($this->bloquesDignatarios() as $bloque => $configuracion) {
            foreach ($configuracion['cargos'] as $cargo => $label) {
                $afiliadoId = filter_var($postDignatarios[$cargo] ?? null, FILTER_VALIDATE_INT, [
                    'options' => ['min_range' => 1],
                ]);

                if ($afiliadoId === false) {
                    $this->json(['ok' => false, 'message' => "Debe seleccionar el afiliado para {$label}."], 422);
                }

                $dignatarios[] = [
                    'bloque' => $bloque,
                    'cargo' => $cargo,
                    'afiliado_id' => (int) $afiliadoId,
                ];
            }
        }

        return [$data, $dignatarios];
    }

    private function validarFechaFormulario(mixed $value, string $label, bool $requerida = false): ?string
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            if ($requerida) {
                $this->json(['ok' => false, 'message' => "La {$label} es obligatoria."], 422);
            }

            return null;
        }

        $fecha = DateTime::createFromFormat('Y-m-d', $value);
        if (!$fecha || $fecha->format('Y-m-d') !== $value) {
            $this->json(['ok' => false, 'message' => "La {$label} debe tener formato YYYY-MM-DD."], 422);
        }

        return $value;
    }

    private function nullSiVacio(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function render(string $view, array $data = []): void
    {
        $viewFile = APP_PATH . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException('Vista no encontrada: ' . $view);
        }

        extract($data);
        require APP_PATH . '/views/layouts/main.php';
    }

    private function requiereLogin(): void
    {
        if (!$this->estaAutenticado()) {
            $this->redirect('login');
        }
    }

    private function estaAutenticado(): bool
    {
        return !empty($_SESSION['usuario']);
    }

    private function soloPost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->json(['ok' => false, 'message' => 'Metodo no permitido.'], 405);
        }
    }

    private function redirect(string $ruta): void
    {
        header('Location: ' . BASE_URL . 'index.php?ruta=' . urlencode($ruta));
        exit;
    }

    private function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function responderError(Throwable $e): void
    {
        if ($this->quiereJson()) {
            $this->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }

        http_response_code(500);
        echo '<h1>Error de aplicacion</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    }

    private function quiereJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $ajax = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return stripos($accept, 'application/json') !== false || strtolower($ajax) === 'xmlhttprequest';
    }

    private function tiposIdentificacion(): array
    {
        return [
            'CC' => 'Cedula de ciudadania',
            'TI' => 'Tarjeta de identidad',
            'CE' => 'Cedula de extranjeria',
            'PA' => 'Pasaporte',
            'NIT' => 'NIT',
        ];
    }

    private function comitesTrabajo(): array
    {
        return [
            'Salud',
            'Educacion',
            'Deportes',
            'Cultura',
            'Medio ambiente',
            'Obras',
            'Convivencia',
            'Seguridad',
            'Adulto mayor',
            'Juventud',
        ];
    }

    private function tiposComunicacion(): array
    {
        return [
            'enviada' => 'Enviada',
            'recibida' => 'Recibida',
        ];
    }

    private function mediosRadicacion(): array
    {
        return [
            'pagina_web' => 'Pagina web',
            'correo' => 'Correo',
            'presencial' => 'Presencial',
            'otro' => 'Otro',
        ];
    }

    private function bloquesDignatarios(): array
    {
        return [
            'directivos' => [
                'label' => 'Directivos',
                'icono' => 'fas fa-user-tie',
                'cargos' => [
                    'presidente' => 'Presidente',
                    'vicepresidente' => 'Vicepresidente',
                    'secretario' => 'Secretario',
                    'tesorero' => 'Tesorero',
                ],
            ],
            'fiscal' => [
                'label' => 'Fiscal',
                'icono' => 'fas fa-balance-scale',
                'cargos' => [
                    'fiscal' => 'Fiscal',
                    'fiscal_suplente' => 'Suplente',
                ],
            ],
            'conciliadores' => [
                'label' => 'Conciliadores',
                'icono' => 'fas fa-handshake',
                'cargos' => [
                    'conciliador_1' => 'Conciliador 1',
                    'conciliador_2' => 'Conciliador 2',
                    'conciliador_3' => 'Conciliador 3',
                ],
            ],
            'delegados' => [
                'label' => 'Delegados',
                'icono' => 'fas fa-id-badge',
                'cargos' => [
                    'delegado_principal_1' => 'Delegado principal 1',
                    'delegado_principal_2' => 'Delegado principal 2',
                    'delegado_principal_3' => 'Delegado principal 3',
                    'delegado_suplente_1' => 'Delegado suplente 1',
                    'delegado_suplente_2' => 'Delegado suplente 2',
                    'delegado_suplente_3' => 'Delegado suplente 3',
                ],
            ],
            'comisiones_trabajo' => [
                'label' => 'Comisiones de trabajo',
                'icono' => 'fas fa-people-carry',
                'cargos' => [
                    'comision_deportes' => 'Deportes',
                    'comision_medio_ambiente' => 'Medio ambiente',
                    'comision_ninez' => 'Ninez',
                    'comision_obras' => 'Obras',
                ],
            ],
        ];
    }

    private function asambleaAfiliadosActivos(): void
    {
        $this->requiereLogin();
        header('Content-Type: application/json; charset=utf-8');

        $modelo = new Asamblea();
        echo json_encode([
            'data' => $modelo->listarAfiliadosActivosAsistencia()
        ]);
    }

    private function asambleaResumenQuorum(): void
    {
        $this->requiereLogin();
        header('Content-Type: application/json; charset=utf-8');

        $modelo = new Asamblea();
        echo json_encode([
            'ok' => true,
            'data' => $modelo->obtenerResumenQuorum()
        ]);
    }

    private function tesoreria(): void
    {
        $this->requiereLogin();

        $this->render(
            'tesoreria/index',
            [
                'titulo' => 'Tesorería'
            ]
        );
    }

    private function tesoreriaListar(): void
    {
        $this->requiereLogin();

        $this->json([
            'data' => $this->tesoreria->listar()
        ]);
    }

    private function tesoreriaGuardar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);
        $data = $this->validarMovimientoTesoreria();

        try {
            $this->tesoreria->guardar($data, $id);

            $mensaje = $id > 0
                ? 'Movimiento actualizado correctamente.'
                : 'Movimiento registrado correctamente.';

            $this->json([
                'ok' => true,
                'message' => $mensaje
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23503') {
                $this->json([
                    'ok' => false,
                    'message' => 'La categoria seleccionada no existe.'
                ], 409);
            }

            throw $e;
        }
    }

    private function tesoreriaEditar(): void
    {
        $this->requiereLogin();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'ok' => false,
                'message' => 'ID de movimiento invalido.'
            ], 422);
        }

        $movimiento = $this->tesoreria->buscarPorId($id);

        if (!$movimiento) {
            $this->json([
                'ok' => false,
                'message' => 'Movimiento no encontrado.'
            ], 404);
        }

        $this->json([
            'ok' => true,
            'data' => $movimiento
        ]);
    }

    private function tesoreriaEliminar(): void
    {
        $this->requiereLogin();
        $this->soloPost();

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'ok' => false,
                'message' => 'ID de movimiento invalido.'
            ], 422);
        }

        $this->tesoreria->eliminar($id);

        $this->json([
            'ok' => true,
            'message' => 'Movimiento eliminado correctamente.'
        ]);
    }

    private function validarMovimientoTesoreria(): array
    {
        $data = [
            'fecha' => trim($_POST['fecha'] ?? ''),
            'tipo_movimiento' => trim($_POST['tipo_movimiento'] ?? ''),
            'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
            'concepto' => trim($_POST['concepto'] ?? ''),
            'valor' => trim($_POST['valor'] ?? ''),
            'medio_pago' => trim($_POST['medio_pago'] ?? ''),
            'numero_soporte' => trim($_POST['numero_soporte'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? ''),
        ];

        $fecha = DateTime::createFromFormat('Y-m-d', $data['fecha']);

        if (!$fecha || $fecha->format('Y-m-d') !== $data['fecha']) {
            $this->json([
                'ok' => false,
                'message' => 'La fecha debe tener formato YYYY-MM-DD.'
            ], 422);
        }

        if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'], true)) {
            $this->json([
                'ok' => false,
                'message' => 'El tipo de movimiento no es valido.'
            ], 422);
        }

        if ($data['categoria_id'] <= 0) {
            $this->json([
                'ok' => false,
                'message' => 'Debe seleccionar una categoria.'
            ], 422);
        }

        if ($data['concepto'] === '') {
            $this->json([
                'ok' => false,
                'message' => 'El concepto es obligatorio.'
            ], 422);
        }

        if (!is_numeric($data['valor']) || (float) $data['valor'] <= 0) {
            $this->json([
                'ok' => false,
                'message' => 'El valor debe ser mayor a cero.'
            ], 422);
        }

        $data['valor'] = (float) $data['valor'];

        return $data;
    }

    private function tesoreriaCategoriasPorTipo(): void
    {
        $this->requiereLogin();

        $tipo = trim($_GET['tipo_movimiento'] ?? '');

        if (!in_array($tipo, ['entrada', 'salida'], true)) {
            $this->json([
                'ok' => false,
                'message' => 'Tipo de movimiento invalido.'
            ], 422);
        }

        $this->json([
            'ok' => true,
            'data' => $this->tesoreria->listarCategoriasPorTipo($tipo)
        ]);
    }

    private function tesoreriaResumen(): void
    {
        $this->requiereLogin();

        $resumen = $this->tesoreria->resumen();

        $this->json([
            'ok' => true,
            'data' => $resumen
        ]);
    }
}
