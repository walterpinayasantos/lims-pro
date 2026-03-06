<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Config\Database;
use PDO;

class Exam extends Model
{
    protected string $table = 'lab_tests';
    protected PDO $db;

    public function __construct()
    {
        // Usamos la conexión estática de tu Database.php
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene el catálogo agrupado por áreas
     */
    public function getCatalogGrouped(): array
    {
        $sql = "SELECT a.name as area_name, a.color_ui, t.* FROM lab_areas a
                JOIN lab_tests t ON a.id = t.area_id
                WHERE t.is_active = 1
                ORDER BY a.name, t.name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un examen específico por ID para el modal
     */
    public function getExamById(int $id): ?array
    {
        $sql = "SELECT t.*, a.name as area_name 
                FROM lab_tests t 
                JOIN lab_areas a ON t.area_id = a.id 
                WHERE t.id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Actualiza los datos del examen incluyendo los nuevos campos clínicos
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE lab_tests SET 
                name = :name, 
                unit = :unit, 
                result_type = :result_type,
                loinc_code = :loinc_code,
                clinical_description = :clinical_description,
                clinical_significance = :clinical_significance,
                updated_at = NOW() 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id'                    => $id,
            'name'                  => $data['name'],
            'unit'                  => $data['unit'],
            'result_type'           => $data['result_type'],
            'loinc_code'            => $data['loinc_code'] ?? null,
            'clinical_description'  => $data['clinical_description'] ?? null,
            'clinical_significance' => $data['clinical_significance'] ?? null
        ]);
    }

    /**
     * Obtiene los rangos de referencia de un examen
     */
    public function getRanges(int $testId): array
    {
        $sql = "SELECT * FROM lab_ref_ranges WHERE test_id = :test_id ORDER BY gender, age_min_days";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['test_id' => $testId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
