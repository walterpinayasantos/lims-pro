<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class Patient extends Model
{

    protected string $table = 'patients';

    /**
     * Obtiene la lista optimizada para DataTables
     * Calcula la edad en base de datos (Performance Boost)
     */
    public function getAllForList(): array
    {
        try {
            $sql = "SELECT 
                        id, 
                        document_id, 
                        first_name,
                        last_name,
                        birth_date,
                        TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) as age, 
                        gender, 
                        phone,
                        email,
                        deleted_at
                    FROM {$this->table} 
                    WHERE deleted_at IS NULL 
                    ORDER BY created_at DESC 
                    LIMIT 1000"; // Limitamos por seguridad inicial

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En producción, loguear el error internamente: error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si existe un documento (Validación Pre-Analítica)
     */
    public function findByDocument(string $docId): ?array
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE document_id = :docId AND deleted_at IS NULL");
        $stmt->execute([':docId' => $docId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Verifica si un CI/DNI ya existe (Regla de Negocio Crítica)
     */
    public function isDocumentRegistered(string $docId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE document_id = :docId AND deleted_at IS NULL");
        $stmt->execute([':docId' => $docId]);
        return (bool)$stmt->fetch();
    }


    /**
     * Crea el paciente y retorna su ID
     */
    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} 
                (document_id, first_name, last_name, birth_date, gender, email, phone, address, nit_ci_invoice, invoice_name, created_by) 
                VALUES 
                (:document_id, :first_name, :last_name, :birth_date, :gender, :email, :phone, :address, :nit, :inv_name, :created_by)";

        $stmt = $this->db->prepare($sql);

        // Manejo de nulos para campos opcionales
        $stmt->execute([
            ':document_id' => $data['document_id'],
            ':first_name'  => strtoupper($data['first_name']), // Estandarizamos a Mayúsculas
            ':last_name'   => strtoupper($data['last_name']),
            ':birth_date'  => $data['birth_date'],
            ':gender'      => $data['gender'],
            ':email'       => !empty($data['email']) ? $data['email'] : null,
            ':phone'       => !empty($data['phone']) ? $data['phone'] : null,
            ':address'     => !empty($data['address']) ? $data['address'] : null,
            ':nit'         => !empty($data['nit_ci_invoice']) ? $data['nit_ci_invoice'] : null,
            ':inv_name'    => !empty($data['invoice_name']) ? $data['invoice_name'] : null,
            ':created_by'  => $_SESSION['user']['id'] ?? 1 // Fallback temporal
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Busca un paciente por su ID (Vital para Edición)
     */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Actualiza los datos de un paciente
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET 
                document_id = :document_id,
                first_name  = :first_name,
                last_name   = :last_name,
                birth_date  = :birth_date,
                gender      = :gender,
                email       = :email,
                phone       = :phone,
                address     = :address,
                nit_ci_invoice = :nit,
                invoice_name   = :inv_name
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'          => $id,
            ':document_id' => $data['document_id'],
            ':first_name'  => strtoupper($data['first_name']),
            ':last_name'   => strtoupper($data['last_name']),
            ':birth_date'  => $data['birth_date'],
            ':gender'      => $data['gender'],
            ':email'       => !empty($data['email']) ? $data['email'] : null,
            ':phone'       => !empty($data['phone']) ? $data['phone'] : null,
            ':address'     => !empty($data['address']) ? $data['address'] : null,
            ':nit'         => !empty($data['nit_ci_invoice']) ? $data['nit_ci_invoice'] : null,
            ':inv_name'    => !empty($data['invoice_name']) ? $data['invoice_name'] : null
        ]);
    }

    /**
     * Eliminado Lógico (Soft Delete)
     * Marca el registro como borrado sin destruirlo.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtiene SOLO los pacientes eliminados (Papelera)
     */
    public function getAllDeleted(): array
    {
        $sql = "SELECT 
                    id, 
                    document_id, 
                    first_name,
                    last_name,
                    birth_date,
                    TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) as age, 
                    gender, 
                    phone,
                    email,
                    deleted_at
                FROM {$this->table} 
                WHERE deleted_at IS NOT NULL 
                ORDER BY deleted_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Restaura un paciente eliminado (Undelete)
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
