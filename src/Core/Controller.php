<?php

declare(strict_types=1);

namespace App\Core;

class Controller
{
    /**
     * Renderiza una vista dentro de un layout.
     * * @param string $view   Nombre de la vista (ej: 'auth/login', 'dashboard/index')
     * @param array  $data   Datos a pasar a la vista (ej: ['title' => 'Bienvenido'])
     * @param string|null $layout Nombre del layout a usar (ej: 'main', 'auth'). Null para sin layout.
     */
    protected function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        // 1. Extraer los datos para que sean variables nativas en la vista
        // Ej: ['user' => 'Juan'] se convierte en la variable $user = 'Juan'
        extract($data);

        // 2. Definir la ruta física del contenido interno (La "carne" del sándwich)
        // Asumimos que todas las vistas de página están en src/Views/pages/
        $contentPath = APP_PATH . "src/Views/pages/{$view}.php";

        // Verificación de seguridad para desarrollo
        if (!file_exists($contentPath)) {
            $this->stopCheck("Error LIMS: La vista <b>{$view}</b> no existe en:<br><code>{$contentPath}</code>");
        }

        // 3. Renderizado
        if ($layout) {
            // Si hay layout, buscamos el archivo "envoltorio"
            $layoutPath = APP_PATH . "src/Views/layouts/{$layout}.php";

            if (file_exists($layoutPath)) {
                // El layout se encarga de hacer 'require_once $contentPath;' en su interior
                require_once $layoutPath;
            } else {
                $this->stopCheck("Error LIMS: El layout <b>{$layout}</b> no existe en:<br><code>{$layoutPath}</code>");
            }
        } else {
            // Si no hay layout (ej: modal content), cargamos directo la vista
            require_once $contentPath;
        }
    }

    /**
     * Helper para devolver respuestas JSON estandarizadas (API / AJAX).
     */
    protected function jsonResponse(array $payload, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }

    /**
     * Helper privado para detener la ejecución con estilo (Solo desarrollo)
     */
    private function stopCheck(string $message): void
    {
        echo "<div style='background:#f8d7da; color:#721c24; padding:20px; border:1px solid #f5c6cb; font-family:sans-serif; margin:20px;'>";
        echo "<h3>LIMS System Halt</h3>";
        echo "<p>{$message}</p>";
        echo "</div>";
        exit;
    }
}
