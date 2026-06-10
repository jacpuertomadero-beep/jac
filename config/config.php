<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('APP_NAME', 'JAC Afiliados');
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');

define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'jac');
define('DB_USER', 'postgres');
define('DB_PASS', 'root');

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = rtrim($scriptDir, '/');
define('BASE_URL', ($basePath === '' || $basePath === '.') ? '/' : $basePath . '/');
