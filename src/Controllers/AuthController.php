<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\SecurityService;

class AuthController extends Controller
{
    private User $userModel;
    private SecurityService $security;

    public function __construct()
    {
        $this->userModel = new User();
        $this->security = new SecurityService();
    }

    public function index(): void
    {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            header('Location: ' . BASE_URL . 'dashboard');
            exit;
        }

        $data = [
            'page_title' => 'Acceso al Sistema',
            'extra_js'   => ['assets/js/modules/auth/login.js']
        ];

        $this->render('auth/login', $data, 'auth');
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // 1. Buscamos el usuario en la DB
        $user = $this->userModel->findForAuth($username);

        // 2. Verificamos si existe y si la contraseña es correcta
        if ($user && $this->security->verifyPassword($password, $user['password'])) {

            // 3. Validamos estados
            if ((int)$user['status'] === 0 || (int)$user['role_status'] === 0) {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'Usuario o Rol inhabilitado.'
                ], 403);
            }

            // 4. Seteamos la sesión (Lo que tus partials necesitan)
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['role_id']   = (int)$user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['avatar']    = $user['avatar'] ?? 'avatar-1.jpg';

            // Permisos básicos para que el sidebar no de error
            $_SESSION['permisos']  = ['dashboard' => ['r' => 1]];

            // 5. Auditoría
            $this->userModel->updateLastLogin((int)$user['id']);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Bienvenido al sistema.'
            ]);
        } else {
            // Si llega aquí, el usuario no existe O la contraseña falló
            $this->jsonResponse([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos.'
            ], 401);
        }
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: ' . BASE_URL . 'auth');
        exit;
    }
}
