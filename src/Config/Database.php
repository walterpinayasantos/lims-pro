<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use Exception;

/**
 * Clase Database: Gestión de conexión segura mediante variables de entorno.
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                // Obtenemos los datos del .env usando getenv() o $_ENV
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $db   = $_ENV['DB_NAME'] ?? 'lims';
                $user = $_ENV['DB_USER'] ?? 'root';
                $pass = $_ENV['DB_PASS'] ?? '';

                // El DSN ahora es dinámico y limpio
                $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];

                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // En producción, loguear el error y mostrar un mensaje genérico.
                // Nunca exponer credenciales en el mensaje de error.
                error_log("Error de Conexión LIMS: " . $e->getMessage());
                throw new Exception("Error interno: No se pudo establecer la conexión con la base de datos.");
            }
        }
        return self::$connection;
    }
}
