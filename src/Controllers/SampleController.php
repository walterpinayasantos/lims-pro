<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class SampleController extends Controller
{
    /**
     * Dashboard de Flebotomía (Lista de Trabajo)
     */
    public function index(): void
    {
        // 1. Conexión a BD
        $db = (new \App\Config\Database())->getConnection();

        // 2. Consulta Lógica: Traer órdenes que tengan items PENDIENTES de toma
        $sql = "SELECT DISTINCT o.id, o.code, o.created_at, 
                       p.first_name, p.last_name, p.document_id, p.gender, p.birth_date
                FROM orders o
                INNER JOIN patients p ON o.patient_id = p.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.sample_status = 'PENDING' 
                  AND o.status != 'CANCELED'
                ORDER BY o.created_at ASC";

        $stmt = $db->query($sql);
        $pendingOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 3. DEFINIR LOS ASSETS (Aquí está la corrección clave)
        // Cargamos el JS modular que maneja los botones y checkboxes
        $extra_js = [
            'assets/js/modules/sample/collection.js'
        ];

        // 4. RENDERIZAR VISTA
        // Pasamos: Vista, Datos, Layout ('main'), CSS ([]), y JS ($extra_js)
        $this->render('sample/index', [
            'pendingOrders' => $pendingOrders,
            'page_title' => 'Toma de Muestras (Flebotomía)'
        ], 'main', [], $extra_js);
    }

    /**
     * Obtener detalle de tubos e instrucciones desde METADATA
     */
    public function getPendingItems(string $orderId): void
    {
        $db = (new \App\Config\Database())->getConnection();

        // AQUÍ ESTÁ LA MAGIA: JOIN con lab_test_metadata
        $sql = "SELECT 
                    oi.id, 
                    oi.sample_status, 
                    t.code, 
                    t.name, 
                    /* Datos de tu tabla Metadata */
                    COALESCE(m.container_type, 'Contenedor Genérico') as container_type,
                    COALESCE(m.sample_type, 'Muestra Estándar') as sample_type,
                    COALESCE(m.sample_volume, '-') as sample_volume,
                    m.patient_preparation,
                    m.biosafety_level
                FROM order_items oi
                INNER JOIN lab_tests t ON oi.lab_test_id = t.id
                /* LEFT JOIN por si algún examen nuevo aún no tiene metadata cargada */
                LEFT JOIN lab_test_metadata m ON t.id = m.test_id
                WHERE oi.order_id = :order_id AND oi.sample_status = 'PENDING'";

        $stmt = $db->prepare($sql);
        $stmt->execute(['order_id' => $orderId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->jsonResponse(['success' => true, 'items' => $items]);
    }

    /**
     * Confirmar Recolección
     */
    public function collect(): void
    {
        // ... (Igual que la versión anterior: UPDATE sample_status = 'COLLECTED') ...
        $input = json_decode(file_get_contents('php://input'), true);
        $itemIds = $input['item_ids'] ?? [];

        if (empty($itemIds)) {
            $this->jsonResponse(['success' => false, 'message' => 'No seleccionó ninguna muestra.'], 400);
        }

        $db = (new \App\Config\Database())->getConnection();

        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $sql = "UPDATE order_items 
                SET sample_status = 'COLLECTED', 
                    sample_collected_at = NOW() 
                WHERE id IN ($placeholders)";

        $stmt = $db->prepare($sql);
        $stmt->execute($itemIds);

        $this->jsonResponse(['success' => true, 'message' => 'Muestras recolectadas correctamente.']);
    }
}
