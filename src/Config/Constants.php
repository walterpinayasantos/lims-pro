<?php

declare(strict_types=1);

/**
 * LIMS ARCHITECT PRO - Configuración de Constantes Globales
 */

// 1. Detección de Protocolo y Host
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// 2. Definición de RUTA RAIZ DEL PROYECTO (Auto-detección)
// Esto detecta automágicamente la carpeta donde está el script, ej: /lims/public
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = $protocol . $host . $scriptName . '/';

// Corrección por si dirname devuelve backslashes en Windows (\)
$baseUrl = str_replace('\\', '/', $baseUrl);

// Aseguramos que termine en /
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}

define('BASE_URL', $baseUrl);

// 3. Entorno
if ($host === 'localhost' || $host === '127.0.0.1') {
    define('ENVIRONMENT', 'development');
} else {
    define('ENVIRONMENT', 'production');
}

// 4. Rutas Físicas del Servidor
define('APP_PATH', dirname(__DIR__, 2) . '/');
define('SRC_PATH', APP_PATH . 'src/');
define('VIEWS_PATH', SRC_PATH . 'Views/');
define('UPLOAD_PATH', APP_PATH . 'public/uploads/');
define('ASSETS_URL', BASE_URL . 'assets/');
