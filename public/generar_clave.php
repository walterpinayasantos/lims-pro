<?php

declare(strict_types=1);

// 1. Configuramos el costo para que coincida con nuestro SecurityService
$options = ['cost' => 12];
$password = $_GET['pass'] ?? 'admin123';

// 2. Generamos el Hash usando BCRYPT explícitamente
$hash = password_hash($password, PASSWORD_BCRYPT, $options);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Generador de Hash Senior - LIMS</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 50px;
            background: #f4f5f7;
            color: #333;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: 0 auto;
        }

        h3 {
            color: #727cf5;
            margin-top: 0;
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            padding: 12px 25px;
            background: #727cf5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #5b65d9;
        }

        .result-box {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            border: 1px dashed #727cf5;
            position: relative;
        }

        code {
            display: block;
            word-break: break-all;
            font-family: 'Consolas', monospace;
            font-size: 15px;
            color: #d63384;
        }

        .copy-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            font-size: 12px;
            background: #6c757d;
        }

        .alert {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h3>🔐 Generador de Hash LIMS (Bcrypt)</h3>
        <p>Escribe la clave que usarás en el login para generar su hash compatible.</p>

        <form action="" method="GET" class="input-group">
            <input type="text" name="pass" value="<?= htmlspecialchars($password) ?>" autofocus>
            <button type="submit">Generar Hash</button>
        </form>

        <div class="result-box">
            <button class="copy-btn" onclick="copyHash()">Copiar</button>
            <code id="hashCode"><?= $hash ?></code>
        </div>

        <div class="alert">
            <strong>Instrucciones Senior:</strong><br>
            1. Copia el hash de arriba.<br>
            2. Ve a tu base de datos (Navicat/phpMyAdmin).<br>
            3. Ejecuta: <code>UPDATE users SET password = 'EL_HASH_COPIADO' WHERE username = 'admin';</code>
        </div>
    </div>

    <script>
        function copyHash() {
            const text = document.getElementById('hashCode').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Hash copiado al portapapeles');
            });
        }
    </script>
</body>

</html>