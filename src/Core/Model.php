<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;

class Model
{
    protected PDO $db;
    protected string $table = ''; // Propiedad para definir la tabla en el hijo

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Busca un registro por su ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Obtiene todos los registros de la tabla
     */
    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

        // Añade esto a tu archivo src/Core/Model.php

    /**
     * Actualiza un registro de forma genérica
     */
    public function update(int $id, array $data): bool
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";
        $data['id'] = $id; // Añadimos el ID al array de ejecución

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
}
