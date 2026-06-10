<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once APP_PATH . '/core/Conexion.php';
require_once APP_PATH . '/models/Usuario.php';
require_once APP_PATH . '/models/Afiliado.php';
require_once APP_PATH . '/models/Asamblea.php';
require_once APP_PATH . '/models/Comunicacion.php';
require_once APP_PATH . '/models/Organizacion.php';
require_once APP_PATH . '/controllers/AppController.php';
require_once APP_PATH . '/models/Tesoreria.php';

$controller = new AppController();
$controller->dispatch();
