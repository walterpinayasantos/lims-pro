@echo off
setlocal
TITLE LIMS ARCHITECT PRO - DEBUG INSTALLER
CLS

echo ========================================================
echo   DIAGNOSTICO DE SISTEMA E INSTALACION
echo ========================================================
echo.

:: 1. Verificando donde estamos
echo [INFO] Directorio actual: %CD%
echo [INFO] Intentando crear estructura aqui...
echo.

:: 2. Prueba de Permisos
mkdir test_permission >nul 2>&1
if not exist "test_permission" (
    color 4F
    echo [ERROR FATAL] NO TENGO PERMISOS DE ESCRITURA AQUI.
    echo --------------------------------------------------
    echo Solucion:
    echo 1. Mueve este archivo a una carpeta como C:\xampp\htdocs\lims
    echo 2. No lo ejecutes en el Escritorio o C:\ directamente.
    pause
    exit
)
rmdir test_permission
echo [OK] Permisos de escritura confirmados.
echo.

:: 3. Creacion de Carpetas (Con comillas para evitar errores de espacios)
echo [1/4] Creando Directorios...

mkdir "src\Config" 2>nul
mkdir "src\Core" 2>nul
mkdir "src\Controllers" 2>nul
mkdir "src\Models" 2>nul
mkdir "src\Services" 2>nul
mkdir "src\Helpers" 2>nul

mkdir "src\Views\layouts" 2>nul
mkdir "src\Views\partials" 2>nul
mkdir "src\Views\pages\dashboard" 2>nul
mkdir "src\Views\pages\reception" 2>nul
mkdir "src\Views\pages\laboratory" 2>nul
mkdir "src\Views\pages\billing" 2>nul

mkdir "public\assets\css" 2>nul
mkdir "public\assets\js\modules" 2>nul
mkdir "public\assets\images" 2>nul
mkdir "public\uploads\patients" 2>nul
mkdir "public\uploads\reports" 2>nul

echo [OK] Directorios creados.

:: 4. Creacion de Archivos (Metodo simplificado)
echo [2/4] Generando Archivos Core...

(
echo ^<?php
echo namespace App\Core;
echo class Router {
echo     // LIMS Router
echo }
) > src\Core\Router.php

(
echo ^<?php
echo namespace App\Core;
echo class Controller {
echo     // Base Controller
echo }
) > src\Core\Controller.php

(
echo ^<?php
echo namespace App\Core;
echo class Model {
echo     // Base Model
echo }
) > src\Core\Model.php

echo [OK] Core generado.

:: 5. Generando Modelos y Servicios
echo [3/4] Generando Lógica de Negocio...

(
echo ^<?php
echo namespace App\Models;
echo use App\Core\Model;
echo class Patient extends Model {
echo     protected $table = 'patients';
echo }
) > src\Models\Patient.php

(
echo ^<?php
echo namespace App\Services;
echo class PatientService {
echo     public function create^(array $data^): bool {
echo         return true;
echo     }
echo }
) > src\Services\PatientService.php

(
echo ^<?php
echo namespace App\Controllers;
echo use App\Core\Controller;
echo class PatientController extends Controller {
echo     // Logic here
echo }
) > src\Controllers\PatientController.php

echo [OK] Modelos y Servicios generados.

:: 6. Archivos Raiz
echo [4/4] Configurando entorno...

(
echo {
echo     "autoload": {
echo         "psr-4": {
echo             "App\\": "src/"
echo         }
echo     }
echo }
) > composer.json

echo /vendor/ > .gitignore
echo .env >> .gitignore
echo /src/Config/Database.php >> .gitignore

echo.
echo ========================================================
echo   INSTALACION COMPLETADA
echo ========================================================
echo   Por favor revisa que las carpetas src y public existan.
echo ========================================================
pause