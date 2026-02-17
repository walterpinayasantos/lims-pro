<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Class Controller
 * Clase base para todos los controladores del LIMS.
 * Maneja la inicialización de sesiones, renderizado de vistas y respuestas JSON.
 */
class Controller
{
    /**
     * Constructor Base
     * Se ejecuta automáticamente al llamar parent::__construct() en los hijos.
     */
    public function __construct()
    {
        // 1. Gestión Centralizada de Sesiones
        // Vital para que $_SESSION funcione en PatientController y en todo el sistema.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Aquí a futuro agregaremos validación de Auth (Login) global.
    }

    /**
     * Renderiza una vista dentro de un layout (Wrapper V2 con Assets).
     * * @param string $viewPath Ruta de la vista relativa a 'pages/' (ej: 'patients/index')
     * @param array  $data     Datos a pasar a la vista (ej: ['user' => ...])
     * @param string|null $layout Nombre del layout (ej: 'main'). Null para vista cruda.
     * @param array  $extra_css Archivos CSS adicionales para este módulo.
     * @param array  $extra_js  Archivos JS adicionales para este módulo.
     */
    protected function render(string $viewPath, array $data = [], ?string $layout = 'main', array $extra_css = [], array $extra_js = []): void
    {
        // 1. Extraer datos (convierte claves de array en variables)
        extract($data);

        // 2. Definir rutas físicas
        // Usamos APP_PATH si está definido, sino calculamos ruta relativa
        $baseDir = defined('APP_PATH') ? APP_PATH : dirname(__DIR__, 2) . '/';
        $contentPath = $baseDir . "src/Views/pages/{$viewPath}.php";

        // 3. Validar existencia de la vista
        if (!file_exists($contentPath)) {
            $this->stopCheck("Error 404 LIMS: La vista <b>{$viewPath}</b> no existe en:<br><code>{$contentPath}</code>");
        }

        // 4. Renderizado
        if ($layout) {
            $layoutPath = $baseDir . "src/Views/layouts/{$layout}.php";

            if (file_exists($layoutPath)) {
                // El layout (main.php) tendrá acceso a $contentPath, $extra_js, $extra_css y todas las variables de $data
                require_once $layoutPath;
            } else {
                $this->stopCheck("Error Crítico: El layout <b>{$layout}</b> no existe en:<br><code>{$layoutPath}</code>");
            }
        } else {
            // Renderizado sin layout (para modales o HTML parcial)
            require_once $contentPath;
        }
    }

    /**
     * Helper para devolver respuestas JSON estandarizadas (AJAX/API).
     * Detiene la ejecución del script inmediatamente después de imprimir.
     */
    protected function jsonResponse(array $payload, int $statusCode = 200): void
    {
        // Limpiar cualquier output previo (espacios en blanco, warnings)
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Helper de depuración para desarrollo.
     */
    private function stopCheck(string $message): void
    {
        echo "<div style='background:#f8d7da; color:#721c24; padding:20px; margin:20px; border:1px solid #f5c6cb; border-radius:5px; font-family:sans-serif;'>
                <h3>Excepción de Arquitectura LIMS</h3>
                <p>{$message}</p>
                <small>Verifique la estructura de directorios en <code>src/Views/</code></small>
              </div>";
        exit;
    }
}
