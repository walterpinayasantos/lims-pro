<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'users';

    /**
     * Busca un usuario activo por username o email incluyendo su rol.
     */
    public function findForAuth(string $identifier): ?array
    {
        // Usamos :user y :email por separado para evitar el error HY093
        $sql = "SELECT u.*, r.name as role_name, r.status as role_status 
            FROM {$this->table} u
            INNER JOIN roles r ON u.role_id = r.id
            WHERE (u.username = :user OR u.email = :email) 
            AND u.deleted_at IS NULL 
            LIMIT 1";

        $stmt = $this->db->prepare($sql);

        // Pasamos el mismo valor a ambos marcadores
        $stmt->execute([
            'user'  => $identifier,
            'email' => $identifier
        ]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        $this->db->prepare($sql)->execute(['id' => $id]);
    }
}
