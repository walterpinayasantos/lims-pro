<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Core\Model; // Usamos la clase base para querys rápidas de pacientes/tests

class OrderController extends Controller
{
    private Order $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
    }

    /**
     * Dashboard Principal de Órdenes (Listado)
     */
    public function index(): void
    {
        $orders = $this->orderModel->getRecentOrders();

        $this->render('order/index', [
            'orders' => $orders,
            'page_title' => 'Gestión de Recepción y Órdenes'
        ]);
    }

    /**
     * Pantalla Principal de Admisión (Crear Orden)
     */
    public function create(): void
    {
        // Cargamos scripts específicos para manejo de cálculos y búsquedas
        $extra_js = [
            'assets/js/modules/orders/admission.js' // Lógica de carrito y cálculos
        ];

        $this->render('order/create', [
            'page_title' => 'Nueva Orden de Laboratorio'
        ], 'main', [], $extra_js);
    }

    /**
     * Procesar el formulario (AJAX)
     */
    public function store(): void
    {
        // 1. Validar Entrada
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['patient_id']) || empty($input['items'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Faltan datos requeridos (paciente o exámenes).'], 400);
        }

        // 2. Cálculos de Seguridad (Backend nunca confía en Frontend)
        $subtotal = 0;
        $cleanItems = [];

        // Instancia rápida para buscar precios reales en BD
        $testModel = new class extends Model {
            protected string $table = 'lab_tests';
        };

        foreach ($input['items'] as $itemId) {
            $test = $testModel->find((int)$itemId);
            if ($test) {
                $price = (float)$test['standard_price'];
                $subtotal += $price;
                $cleanItems[] = ['id' => $test['id'], 'price' => $price];
            }
        }

        // Aplicar descuentos
        $discountPercent = (float)($input['discount_percent'] ?? 0);
        $discountAmount = ($subtotal * $discountPercent) / 100;
        $total = $subtotal - $discountAmount;

        // Validar Pagos
        $paidAmount = (float)($input['paid_amount'] ?? 0);
        if ($paidAmount > $total) $paidAmount = $total; // No cobrar de más

        $balanceDue = $total - $paidAmount;

        // Determinar Estado de Pago
        $paymentStatus = 'UNPAID';
        if ($paidAmount >= $total) $paymentStatus = 'PAID';
        elseif ($paidAmount > 0)   $paymentStatus = 'PARTIAL';

        // 3. Preparar Datos
        $orderData = [
            'code'             => $this->orderModel->generateNextCode(),
            'patient_id'       => (int)$input['patient_id'],
            'doctor_name'      => !empty($input['doctor_name']) ? trim($input['doctor_name']) : null,
            'subtotal'         => $subtotal,
            'discount_percent' => $discountPercent,
            'discount_amount'  => $discountAmount,
            'total_amount'     => $total,
            'paid_amount'      => $paidAmount,
            'balance_due'      => $balanceDue,
            'payment_status'   => $paymentStatus,
            'payment_method'   => $input['payment_method'] ?? 'CASH',
            'status'           => 'PENDING',
            'created_by'       => $_SESSION['user_id'] ?? 1 // Asumiendo ID 1 si no hay sesión
        ];

        try {
            $orderId = $this->orderModel->createTransaction($orderData, $cleanItems);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Orden creada correctamente.',
                'order_id' => $orderId,
                'redirect' => BASE_URL . 'order/print/' . $orderId
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Búsqueda de Pacientes para Select2 (AJAX)
     */
    public function searchPatient(): void
    {
        $term = $_GET['q'] ?? '';
        // Evitamos búsquedas vacías o muy cortas
        if (strlen($term) < 2) {
            $this->jsonResponse(['results' => []]);
            return;
        }

        $db = (new \App\Config\Database())->getConnection();

        // CORRECCIÓN: Usamos :t1, :t2, :t3 para evitar error de parámetros duplicados
        $sql = "SELECT id, first_name, last_name, document_id 
                FROM patients 
                WHERE first_name LIKE :t1 
                   OR last_name LIKE :t2 
                   OR document_id LIKE :t3 
                LIMIT 10";

        $stmt = $db->prepare($sql);
        $wildcard = "%$term%";

        // Pasamos el mismo valor 3 veces, una para cada placeholder
        $stmt->execute([
            't1' => $wildcard,
            't2' => $wildcard,
            't3' => $wildcard
        ]);

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = [
                'id' => $row['id'],
                'text' => $row['document_id'] . ' - ' . $row['first_name'] . ' ' . $row['last_name']
            ];
        }

        $this->jsonResponse(['results' => $results]);
    }

    /**
     * Búsqueda de Exámenes para el Carrito (AJAX)
     */
    public function searchTest(): void
    {
        $term = $_GET['q'] ?? '';
        $db = (new \App\Config\Database())->getConnection();

        // CORRECCIÓN: Usamos :t1 y :t2
        $sql = "SELECT id, code, name, standard_price 
                FROM lab_tests 
                WHERE is_active = 1 
                  AND (name LIKE :t1 OR code LIKE :t2) 
                LIMIT 20";

        $stmt = $db->prepare($sql);
        $wildcard = "%$term%";

        $stmt->execute([
            't1' => $wildcard,
            't2' => $wildcard
        ]);

        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = [
                'id' => $row['id'],
                'text' => ($row['code'] ? $row['code'] . ' - ' : '') . $row['name'],
                'price' => (float)$row['standard_price'] // Aseguramos que sea flotante para JS
            ];
        }

        $this->jsonResponse(['results' => $results]);
    }

    /**
     * Vista de Impresión de Comprobante
     */
    public function print(string $id): void
    {
        $orderId = (int)$id;
        $order = $this->orderModel->getOrderWithPatient($orderId);

        if (!$order) {
            // Si no existe, error 404 amigable
            header('Location: ' . BASE_URL . 'order/create');
            exit;
        }

        $items = $this->orderModel->getOrderItems($orderId);

        // Renderizamos SIN layout ('null') porque el ticket tiene su propio CSS de impresión
        // Pero inyectamos la librería de QR Code vía CDN en la vista
        $this->render('order/print', [
            'order' => $order,
            'items' => $items,
            'page_title' => 'Imprimir Orden #' . $order['code']
        ], null);
    }
}
