<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;
use Exception;

class Order extends Model
{
    protected string $table = 'orders';

    /**
     * Crea una orden completa con sus items dentro de una transacción DB.
     * Retorna el ID de la orden creada o lanza excepción.
     */
    public function createTransaction(array $orderData, array $items): int
    {
        try {
            $this->db->beginTransaction();

            // 1. Insertar Cabecera de Orden
            $sqlOrder = "INSERT INTO orders 
                        (code, patient_id, doctor_name, subtotal, discount_percent, discount_amount, 
                         total_amount, paid_amount, balance_due, payment_status, payment_method, status, created_by)
                        VALUES 
                        (:code, :patient_id, :doctor_name, :subtotal, :discount_percent, :discount_amount,
                         :total_amount, :paid_amount, :balance_due, :payment_status, :payment_method, :status, :created_by)";

            $stmt = $this->db->prepare($sqlOrder);
            $stmt->execute($orderData);

            $orderId = (int)$this->db->lastInsertId();

            // 2. Insertar Items (Exámenes)
            $sqlItem = "INSERT INTO order_items (order_id, lab_test_id, price) VALUES (:order_id, :lab_test_id, :price)";
            $stmtItem = $this->db->prepare($sqlItem);

            foreach ($items as $item) {
                $stmtItem->execute([
                    'order_id'    => $orderId,
                    'lab_test_id' => $item['id'],
                    'price'       => $item['price']
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            // Loguear el error real internamente si tienes un logger
            throw new Exception("Error al procesar la orden: " . $e->getMessage());
        }
    }

    /**
     * Generador de Código de Orden (Ej: ORD-20231025-001)
     * Simple autoincremental por ahora basado en timestamp para evitar colisiones
     */
    public function generateNextCode(): string
    {
        // Formato: ORD-[AÑO][MES][DIA]-[H][M][S] (Simple y único)
        // Para producción masiva se recomienda usar una tabla de secuencias.
        return 'ORD-' . date('ymd-His');
    }

    /**
     * Obtiene la cabecera de la orden con datos del paciente unidos (JOIN)
     */
    public function getOrderWithPatient(int $orderId): ?array
    {
        $sql = "SELECT o.*, 
                       p.first_name, p.last_name, p.document_id, p.birth_date, p.gender, p.phone, p.email
                FROM orders o
                INNER JOIN patients p ON o.patient_id = p.id
                WHERE o.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $orderId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Obtiene los exámenes de una orden
     */
    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT oi.price, t.code, t.name, t.unit
                FROM order_items oi
                INNER JOIN lab_tests t ON oi.lab_test_id = t.id
                WHERE oi.order_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $orderId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el listado de órdenes recientes para el Dashboard
     */
    public function getRecentOrders(int $limit = 100): array
    {
        $sql = "SELECT o.id, o.code, o.created_at, o.total_amount, o.balance_due, o.payment_status, o.status,
                       p.first_name, p.last_name, p.document_id
                FROM orders o
                INNER JOIN patients p ON o.patient_id = p.id
                ORDER BY o.id DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        // Bind directo de entero para LIMIT (PDO a veces da problemas si se pasa como string)
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
