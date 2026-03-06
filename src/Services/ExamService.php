<?php

declare(strict_types=1);

namespace App\Services;

class ExamService
{

    /**
     * Ejecuta una fórmula matemática dinámica basada en variables del laboratorio
     * @param string $expression Ej: (HEM-HCT * 10) / HEM-RBC
     * @param array $variables Ej: ['HEM-HCT' => 45, 'HEM-RBC' => 5]
     */
    public function calculateFormula(string $expression, array $variables): float
    {
        foreach ($variables as $code => $value) {
            $expression = str_replace($code, (string)$value, $expression);
        }

        // Limpieza de seguridad antes de evaluar
        $expression = preg_replace('/[^0-9\+\-\*\/\(\)\. ]/', '', $expression);

        try {
            // Evaluamos la expresión matemática
            $result = eval("return $expression;");
            return (float)$result;
        } catch (\Throwable $th) {
            return 0.0;
        }
    }

    /**
     * Lógica para encontrar el rango correcto basado en el paciente
     */
    public function findMatchingRange(array $ranges, string $patientGender, int $ageInDays): ?array
    {
        foreach ($ranges as $range) {
            // Ajuste para género 'O' (Otros) mapeado a 'B' (Both/Ambos)
            $searchGender = ($patientGender === 'O') ? 'B' : $patientGender;

            if ($range['gender'] === $searchGender || $range['gender'] === 'B') {
                if ($ageInDays >= $range['age_min_days'] && $ageInDays <= $range['age_max_days']) {
                    return $range;
                }
            }
        }
        return null;
    }
}
