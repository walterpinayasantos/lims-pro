<?php

declare(strict_types=1);

namespace App\Config;

use Exception;

class EnvLoader
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception("Archivo .env no encontrado en: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // CORRECCIÓN DE COMPATIBILIDAD:
            // Usamos strpos === 0 en lugar de str_starts_with (PHP 8+)
            // Si la línea empieza con #, es comentario
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            // CORRECCIÓN DE COMPATIBILIDAD:
            // Usamos strpos !== false en lugar de str_contains (PHP 8+)
            if (strpos($line, '=') !== false) {

                // Dividimos solo en el primer '=' encontrado
                [$name, $value] = explode('=', $line, 2);

                $name = trim($name);
                $value = trim($value);

                // Eliminamos comillas si existen en los valores (ej: "root")
                $value = trim($value, '"\'');

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }
}
