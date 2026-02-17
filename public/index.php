<?php

declare(strict_types=1);

// 1. Constantes
require_once __DIR__ . '/../src/Config/Constants.php';

// 2. Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// 3. Entorno y Rutas
use App\Config\EnvLoader;
use App\Core\Router;

try {
    EnvLoader::load(__DIR__ . '/../.env');
    Router::dispatch();
} catch (Exception $e) {
    die("Error crítico: " . $e->getMessage());
}
