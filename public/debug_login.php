<?php
// public/debug_login.php
// Script de Diagnóstico LIMS - Versión corregida para estructura real

require_once __DIR__ . '/../src/Config/EnvLoader.php';
require_once __DIR__ . '/../src/Config/Database.php';

use App\Config\EnvLoader;
use App\Config\Database;

try {
    EnvLoader::load(__DIR__ . '/../.env');
    $db = (new Database())->getConnection();

    // -----------------------------------------
    // AJUSTA ESTOS DATOS SEGÚN LO QUE TENGAS EN LA DB
    $username_a_probar = 'admin'; // Cambiado de email a username según tu SQL
    $password_a_probar = 'admin123';
    // -----------------------------------------

    echo "<h1>🕵️‍♂️ Diagnóstico de Login LIMS</h1>";
    echo "<p>Probando Usuario: <strong>$username_a_probar</strong></p>";
    echo "<p>Probando Password: <strong>$password_a_probar</strong></p>";
    echo "<hr>";

    // 2. Buscar Usuario (Usando username que es lo que tienes en users.sql)
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username_a_probar]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<h3 style='color:red'>❌ ERROR: El usuario no existe en la base de datos.</h3>";
        echo "<p>Revisa la tabla 'users'. El username buscado es: '$username_a_probar'</p>";
        exit;
    }

    echo "<p>✅ Usuario encontrado (ID: " . $user['id'] . ")</p>";
    echo "<p>Email registrado: " . $user['email'] . "</p>";

    // 3. Verificando Password Hash
    echo "<p>Hash en Base de Datos: <code style='background:#eee; padding:5px;'>" . $user['password'] . "</code></p>";

    if (password_verify($password_a_probar, $user['password'])) {
        echo "<h3 style='color:green'>✅ CONTRASEÑA CORRECTA</h3>";
        echo "<p>Bcrypt ha validado el password exitosamente contra el hash de la DB.</p>";
    } else {
        echo "<h3 style='color:red'>❌ CONTRASEÑA INCORRECTA</h3>";
        echo "<p>El hash de la DB NO coincide con la contraseña escrita.</p>";

        // Generamos el hash correcto con el costo 12 que usa nuestro sistema
        $nuevoHash = password_hash($password_a_probar, PASSWORD_BCRYPT, ['cost' => 12]);
        echo "<p><strong>Solución:</strong> Ejecuta esto en tu base de datos:<br>";
        echo "<textarea cols='80' rows='3' style='width:100%'>UPDATE users SET password = '$nuevoHash' WHERE username = '$username_a_probar';</textarea></p>";
    }

    // 4. Verificando Estado (Cambiado de is_active a status)
    echo "<hr><p>Estado del usuario (columna status): <strong>" . $user['status'] . "</strong></p>";
    if ((int)$user['status'] === 1) {
        echo "<p style='color:green'>✅ El usuario está ACTIVO.</p>";
    } else {
        echo "<p style='color:red'>❌ EL USUARIO ESTÁ INACTIVO (Status 0). Por eso el login fallará aunque la clave sea correcta.</p>";
    }
} catch (Exception $e) {
    echo "<h1 style='color:red'>💥 Error de Sistema</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
