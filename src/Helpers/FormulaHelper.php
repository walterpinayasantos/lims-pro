<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * LIMS Formula Engine
 * Procesa expresiones matemáticas transformando códigos de examen en valores reales.
 */
class FormulaHelper
{
    /**
     * Calcula una expresión matemática basada en un mapa de variables.
     * @param string $expression Ej: "(HTO * 10) / RBC"
     * @param array $values Ej: ["HTO" => 45, "RBC" => 5.2]
     * @param int $precision Decimales de redondeo
     */
    public static function calculate(string $expression, array $values, int $precision = 2): ?float
    {
        try {
            // 1. Reemplazar códigos por sus valores numéricos
            foreach ($values as $code => $val) {
                // Aseguramos que el valor sea numérico, sino usamos 0
                $cleanVal = is_numeric($val) ? $val : 0;
                $expression = str_replace(trim((string)$code), (string)$cleanVal, $expression);
            }

            // 2. Saneamiento estricto: Solo permitir números y operadores matemáticos básicos
            // Esto bloquea cualquier intento de inyección de código PHP
            $safeExpression = preg_replace('/[^0-9\+\-\*\/\(\)\. ]/', '', $expression);

            // 3. Evaluación de la expresión
            // Usamos un closure para encapsular la operación
            $result = @eval("return $safeExpression;");

            if ($result === false || $result === null) {
                return null;
            }

            return round((float)$result, $precision);
        } catch (\Throwable $th) {
            error_log("Error en cálculo de fórmula LIMS: " . $th->getMessage());
            return null;
        }
    }
}
