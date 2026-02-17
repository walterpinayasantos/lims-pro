<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;

class Model
{
    /**
     * @var PDO
     * Propiedad protegida para que las clases hijas (User, Patient) puedan usarla.
     */
    protected PDO $db;

    public function __construct()
    {
        // 1. Instanciamos la clase de Configuración de Base de Datos
        $database = new Database();

        // 2. Obtenemos la conexión PDO y la guardamos en la propiedad $db
        $this->db = $database->getConnection();
    }
}
