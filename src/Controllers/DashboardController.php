<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        // El Router ya verifica si está logueado, pero aquí aseguramos
        if (!isset($_SESSION['logged_in'])) {
            header('Location: ' . BASE_URL . 'auth');
            exit;
        }
    }

    public function index(): void
    {
        $roleId = (int)$_SESSION['role_id'];

        // Datos simulados que luego vendrán de modelos (StatsModel)
        $data = [
            'page_title' => 'Panel de Control',
            'user_name'  => $_SESSION['full_name'] ?? 'Usuario',
            'role_name'  => $_SESSION['role_name'] ?? 'Invitado',
            'stats' => [
                'pending_samples' => 12,
                'validated_today' => 45,
                'urgent_tests'    => 3,
                'total_patients'  => 128
            ]
        ];

        $this->render('dashboard/index', $data, 'main');
    }
}
