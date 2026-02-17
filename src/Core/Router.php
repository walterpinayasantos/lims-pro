<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    public static function dispatch(): void
    {
        // 1. Asegurar sesión activa para el sistema LIMS
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Obtener la ruta del .htaccess (route=...) o default a dashboard
        $url = $_GET['route'] ?? 'dashboard';
        $url = trim($url, '/');
        $urlParts = explode('/', $url);

        // 3. Definir Controlador (CamelCase)
        // Ejemplo: 'auth' -> App\Controllers\AuthController
        $controllerKey = !empty($urlParts[0]) ? ucfirst($urlParts[0]) : 'Dashboard';
        $controllerName = "App\\Controllers\\" . $controllerKey . "Controller";

        // 4. Definir Método (Si no existe en URL, por defecto es 'index')
        // Esto permite que /auth/ funcione igual que /auth/index
        $methodName = $urlParts[1] ?? 'index';

        // 5. Verificación de Existencia
        if (class_exists($controllerName)) {
            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                // Extraer parámetros restantes de la URL
                $params = array_slice($urlParts, 2);

                // Ejecutar el controlador y el método
                call_user_func_array([$controller, $methodName], $params);
            } else {
                self::abort("El servicio solicitado [{$methodName}] no está disponible en este módulo.");
            }
        } else {
            // 6. Manejo inteligente de sesión y redirección amigable
            if (!isset($_SESSION['logged_in']) && $controllerKey !== 'Auth') {
                header('Location: ' . BASE_URL . 'auth'); // Redirige a la raíz de auth, no a index
                exit;
            }
            self::abort("El módulo [{$controllerKey}] no ha sido implementado en el sistema.");
        }
    }

    /**
     * Abortar con elegancia profesional
     */
    private static function abort(string $message): void
    {
        // Si es una petición AJAX, respondemos con JSON para no romper el JS
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => $message]);
            exit;
        }

        // Si es carga normal, mostramos el error
        http_response_code(404);
        die("<div style='font-family:sans-serif; padding:50px; text-align:center;'>
                <h1 style='color:#dc3545;'>Error 404 - LIMS Architect</h1>
                <p>{$message}</p>
                <a href='" . BASE_URL . "'>Volver al inicio</a>
             </div>");
    }
}
