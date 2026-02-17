<?php

declare(strict_types=1);

namespace App\Services;

class SecurityService
{
    /**
     * Genera un hash seguro. Si en el futuro cambias a Argon2, 
     * solo modificas el algoritmo aquí.
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica la contraseña contra el hash almacenado.
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Verifica si el hash necesita ser actualizado a un algoritmo más fuerte.
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
