<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class ResultController extends Controller
{
    /**
     * Dashboard de Resultados (Lista de Trabajo Pendiente)
     */
    public function index(): void
    {
        $db = (new \App\Config\Database())->getConnection();

        // CORRECCIÓN: Buscamos si existe en lab_results
        $sql = "SELECT DISTINCT o.id, o.code, o.created_at, 
                       p.first_name, p.last_name, p.document_id, p.gender, p.birth_date,
                       (SELECT COUNT(*) FROM order_items WHERE order_id = o.id AND sample_status = 'COLLECTED') as total_samples
                FROM orders o
                INNER JOIN patients p ON o.patient_id = p.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                WHERE oi.sample_status = 'COLLECTED' 
                  AND o.status != 'CANCELED'
                ORDER BY o.created_at ASC";

        $stmt = $db->query($sql);
        $worklist = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('result/index', [
            'worklist' => $worklist,
            'page_title' => 'Validación Técnica de Resultados'
        ]);
    }

    /**
     * Pantalla de Ingreso de Resultados (Worksheet)
     */
    public function worksheet(string $id): void
    {
        $orderId = (int)$id;
        $db = (new \App\Config\Database())->getConnection();

        // 1. Datos de la Orden (Paciente)
        $stmtOrder = $db->prepare("SELECT o.*, p.first_name, p.last_name, p.birth_date, p.gender 
                                FROM orders o 
                                INNER JOIN patients p ON o.patient_id = p.id 
                                WHERE o.id = :id");
        $stmtOrder->execute(['id' => $orderId]);
        $order = $stmtOrder->fetch(\PDO::FETCH_ASSOC);

        // 2. Consulta de Items (Fiel a tu DDL de lab_test_metadata)
        // Nota: Como 'unit' no aparece en tu DDL de metadata, la buscamos en 't' (lab_tests)
        $sqlItems = "SELECT 
                    oi.id, 
                    t.name AS test_name, 
                    r.result_value AS current_result,
                    r.comments,
                    t.unit,             /* Asumimos que unit está en lab_tests según tu reporte */
                    tm.dec_ref_low,
                    tm.dec_ref_high,
                    tm.critical_low AS crit_ref_low,   /* Mapeo al nombre que espera el JS */
                    tm.critical_high AS crit_ref_high /* Mapeo al nombre que espera el JS */
                 FROM order_items oi
                 INNER JOIN lab_tests t ON oi.lab_test_id = t.id
                 LEFT JOIN lab_test_metadata tm ON t.id = tm.test_id
                 LEFT JOIN lab_results r ON oi.id = r.order_item_id
                 WHERE oi.order_id = :id 
                   AND oi.sample_status = 'COLLECTED'";

        $stmt = $db->prepare($sqlItems);
        $stmt->execute(['id' => $orderId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('result/worksheet', [
            'order' => $order,
            'items' => $items,
            'page_title' => 'Hoja de Trabajo - ' . ($order['code'] ?? '')
        ], 'main', [], ['assets/js/modules/result/worksheet.js']);
    }

    /**
     * Guardar y Validar (AJAX)
     */
    public function store(): void
    {
        // 1. Validación de Seguridad
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['order_id']) || empty($input['results'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Datos incompletos']);
        }

        $orderId = (int)$input['order_id'];
        $db = (new \App\Config\Database())->getConnection();

        // Captura del usuario actual para trazabilidad
        $userId = $_SESSION['user_id'] ?? 1;

        try {
            // Iniciamos la transacción (Todo o Nada)
            $db->beginTransaction();

            // ---------------------------------------------------------
            // FASE 1: Guardado Individual de Resultados
            // ---------------------------------------------------------
            foreach ($input['results'] as $res) {
                $orderItemId = (int)$res['order_item_id'];

                // A. Limpieza previa (evita duplicados si se edita el resultado)
                $db->prepare("DELETE FROM lab_results WHERE order_item_id = :oiid")
                    ->execute(['oiid' => $orderItemId]);

                // B. Inserción del nuevo resultado
                $sqlInsert = "INSERT INTO lab_results (order_item_id, result_value, flag, validated_by, comments) 
                          VALUES (:oiid, :val, 'NORMAL', :vby, :comm)";

                $db->prepare($sqlInsert)->execute([
                    'oiid' => $orderItemId,
                    'val'  => $res['result_value'],
                    'vby'  => $userId,
                    'comm' => $res['comments']
                ]);

                // C. Actualización del ÍTEM a 'COMPLETED'
                // Esto marca que ESTE examen específico ya se terminó.
                $db->prepare("UPDATE order_items SET sample_status = 'COMPLETED' WHERE id = :oiid")
                    ->execute(['oiid' => $orderItemId]);
            }

            // ---------------------------------------------------------
            // FASE 2: Inteligencia LIMS (Cálculo de Estado de la Orden)
            // ---------------------------------------------------------

            // A. Contamos el TOTAL de exámenes que tiene esta orden
            $stmtTotal = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :oid");
            $stmtTotal->execute(['oid' => $orderId]);
            $totalItems = $stmtTotal->fetchColumn();

            // B. Contamos cuántos exámenes ya están TERMINADOS ('COMPLETED')
            // (Incluyendo los que acabamos de guardar en el bucle de arriba)
            $stmtReady = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :oid AND sample_status = 'COMPLETED'");
            $stmtReady->execute(['oid' => $orderId]);
            $completedItems = $stmtReady->fetchColumn();

            // ---------------------------------------------------------
            // FASE 3: Actualización de la Cabecera (Tabla 'orders')
            // ---------------------------------------------------------

            // A. Contamos el TOTAL de exámenes
            $stmtTotal = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :oid");
            $stmtTotal->execute(['oid' => $orderId]);
            $totalItems = $stmtTotal->fetchColumn();

            // B. Contamos cuántos ya están listos ('COMPLETED' en order_items)
            $stmtReady = $db->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = :oid AND sample_status = 'COMPLETED'");
            $stmtReady->execute(['oid' => $orderId]);
            $completedItems = $stmtReady->fetchColumn();

            // C. SEMÁFORO DE ESTADOS (Fiel a tu DDL actualizado)
            if ($totalItems > 0 && $totalItems == $completedItems) {
                // VERDE: Todo listo -> Pasa a la Bandeja de Firmas
                $newState = 'COMPLETED';
            } else {
                // AMARILLO: Aún falta trabajo -> Se queda en Proceso
                $newState = 'IN_PROCESS';
            }

            $db->prepare("UPDATE orders SET status = :st WHERE id = :oid")
                ->execute(['st' => $newState, 'oid' => $orderId]);

            // Confirmamos cambios
            $db->commit();

            $this->jsonResponse([
                'success' => true,
                'message' => 'Resultados guardados correctamente.',
                'status_applied' => ($totalItems == $completedItems) ? 'COMPLETED' : 'PROCESSING'
            ]);
        } catch (\Exception $e) {
            // Si algo falla, revertimos todo para no dejar datos corruptos
            if ($db->inTransaction()) $db->rollBack();
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Bandeja de Firma Facultativa (Post-Analítica)
     */
    /**
     * Muestra la bandeja de órdenes listas para firmar
     */
    public function signature(): void
    {
        $db = (new \App\Config\Database())->getConnection();

        // AHORA SÍ: Podemos filtrar directamente por status = 'COMPLETED'
        // Esto es mucho más rápido y profesional.
        $sql = "SELECT 
                o.id, 
                o.code, 
                o.created_at, 
                p.first_name, 
                p.last_name, 
                p.document_id,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as total_items
            FROM orders o 
            INNER JOIN patients p ON o.patient_id = p.id 
            WHERE o.status = 'COMPLETED' 
            ORDER BY o.created_at ASC";

        $stmt = $db->query($sql);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('result/signature_list', [
            'orders' => $orders,
            'page_title' => 'Firma Facultativa'
        ]);
    }

    /**
     * Vista de Pre-informe para revisar y corregir antes de firmar
     */
    /**
     * Vista de Pre-informe para Revisión Facultativa
     * Esta función recupera los datos guardados por el técnico para que el Bioquímico los valide.
     */

    public function review(string $id): void
    {
        $orderId = (int)$id;
        $db = (new \App\Config\Database())->getConnection();

        // 1. Datos del Paciente y la Orden (Usando la tabla 'orders' correcta)
        $sqlOrder = "SELECT o.*, p.first_name, p.last_name, p.birth_date, p.gender, p.document_id, p.email 
                 FROM orders o 
                 INNER JOIN patients p ON o.patient_id = p.id 
                 WHERE o.id = :id";
        $stmt = $db->prepare($sqlOrder);
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            // Si no existe, volver a la bandeja
            header('Location: ' . BASE_URL . 'result/signature');
            exit;
        }

        // 2. Datos de los Resultados (CORREGIDO)
        // Se cambió m.unit por t.unit
        $sqlResults = "SELECT 
                        t.name as test_name, 
                        t.code as test_code,
                        r.result_value, 
                        r.flag, 
                        r.comments,
                        t.unit,  -- <--- CORRECCIÓN AQUÍ: La unidad viene de lab_tests (t)
                        m.dec_ref_low, 
                        m.dec_ref_high,
                        cat.name as area_name
                   FROM order_items oi
                   INNER JOIN lab_results r ON oi.id = r.order_item_id
                   INNER JOIN lab_tests t ON oi.lab_test_id = t.id
                   LEFT JOIN lab_test_metadata m ON t.id = m.test_id
                   LEFT JOIN lab_areas cat ON t.area_id = cat.id
                   WHERE oi.order_id = :oid
                   ORDER BY cat.name ASC, t.name ASC";

        $stmtRes = $db->prepare($sqlResults);
        $stmtRes->execute(['oid' => $orderId]);
        $results = $stmtRes->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Renderizar la vista de Revisión
        $this->render('result/review_card', [
            'order' => $order,
            'results' => $results,
            'page_title' => 'Validación Facultativa'
        ]);
    }


    /**
     * Procesa la Firma Facultativa Final
     */
    public function approve(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return; // Silencioso si no es POST
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $orderId = (int)$input['order_id'];
        $userId = $_SESSION['user_id'] ?? 1;

        $db = (new \App\Config\Database())->getConnection();

        try {
            // ACTUALIZAMOS EL ESTADO A 'VALIDATED'
            // Fiel a tu DDL actualizado
            $sql = "UPDATE orders SET 
                    status = 'VALIDATED', 
                    updated_at = NOW() 
                WHERE id = :id";

            $db->prepare($sql)->execute(['id' => $orderId]);

            // Opcional: Podríamos guardar un log de "quién firmó" en otra tabla, 
            // pero por ahora el updated_at nos sirve.

            $this->jsonResponse(['success' => true]);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function delivery(): void
    {
        $db = (new \App\Config\Database())->getConnection();

        // Buscamos órdenes VALIDADAS listas para entregar al paciente
        $sql = "SELECT 
                    o.id, 
                    o.code, 
                    o.created_at, 
                    o.updated_at as validated_at, -- Fecha de firma
                    p.first_name, 
                    p.last_name, 
                    p.document_id,
                    p.email
                FROM orders o 
                INNER JOIN patients p ON o.patient_id = p.id 
                WHERE o.status = 'VALIDATED' 
                ORDER BY o.updated_at DESC";
        $stmt = $db->query($sql);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('result/delivery_list', [
            'orders' => $orders,
            'page_title' => 'Entrega de Resultados'
        ]);
    }

    /**
     * Genera un PDF profesional con los resultados validados de la orden.
     * Utiliza la librería DomPDF cargada manualmente (sin Composer).
     * * @param string $id El ID de la orden a imprimir.
     */
    public function print(string $id): void
    {
        $orderId = (int)$id;
        $db = (new \App\Config\Database())->getConnection();

        // ---------------------------------------------------------
        // 1. SEGURIDAD: Verificar que la orden esté VALIDADA
        // ---------------------------------------------------------
        // Regla de Negocio: No se pueden imprimir informes que no hayan sido
        // firmados digitalmente por el Bioquímico (Status: VALIDATED).
        $stmtStatus = $db->prepare("SELECT status FROM orders WHERE id = :id");
        $stmtStatus->execute(['id' => $orderId]);
        $currentStatus = $stmtStatus->fetchColumn();

        // Permitimos imprimir si está VALIDATED (Listo) O DELIVERED (Historial)
        $allowedStatuses = ['VALIDATED', 'DELIVERED'];

        if (!in_array($currentStatus, $allowedStatuses)) {
            die("<h1>Error de Seguridad</h1><p>Este resultado aún no ha sido validado por el Bioquímico (Estado actual: $currentStatus). No se puede generar el informe.</p>");
        }

        // ---------------------------------------------------------
        // 2. RECUPERACIÓN DE DATOS
        // ---------------------------------------------------------

        // A. Datos de Cabecera (Paciente, Orden y Profesional Firmante)
        $sqlOrder = "SELECT o.*, 
                            p.first_name, p.last_name, p.document_id, p.gender, p.birth_date, p.email,
                            u.first_name as bio_name, u.last_name as bio_lastname, 
                            u.digital_signature, u.professional_license
                     FROM orders o
                     INNER JOIN patients p ON o.patient_id = p.id
                     /* Obtenemos datos del usuario que creó/validó para mostrar su firma */
                     LEFT JOIN users u ON o.created_by = u.id 
                     WHERE o.id = :id";

        $stmt = $db->prepare($sqlOrder);
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) exit("Orden no encontrada");

        // B. Datos de Resultados (Agrupados y Detallados)
        // Se corrigió la consulta para obtener la unidad desde lab_tests (t.unit)
        // y asegurar que solo traiga ítems que realmente tienen resultado en lab_results.
        $sqlResults = "SELECT 
                            r.result_value, r.flag, r.comments, r.validated_at,
                            t.name as test_name, 
                            t.unit,  -- Unidad de medida correcta
                            m.dec_ref_low, m.dec_ref_high,
                            a.name as area_name
                       FROM order_items oi
                       INNER JOIN lab_tests t ON oi.lab_test_id = t.id
                       INNER JOIN lab_areas a ON t.area_id = a.id
                       INNER JOIN lab_results r ON oi.id = r.order_item_id
                       LEFT JOIN lab_test_metadata m ON t.id = m.test_id
                       WHERE oi.order_id = :id 
                         AND r.id IS NOT NULL -- Solo lo que tiene valor cargado
                       ORDER BY a.id ASC, t.name ASC";

        $stmtRes = $db->prepare($sqlResults);
        $stmtRes->execute(['id' => $orderId]);
        $rawResults = $stmtRes->fetchAll(\PDO::FETCH_ASSOC);

        // ---------------------------------------------------------
        // 3. LÓGICA DE PRESENTACIÓN (Agrupación)
        // ---------------------------------------------------------
        // Organizamos los resultados por 'Área' (Hematología, Química...)
        // para que el reporte salga ordenado por secciones.
        $groupedResults = [];
        foreach ($rawResults as $row) {
            $area = $row['area_name'];
            if (!isset($groupedResults[$area])) {
                $groupedResults[$area] = [];
            }
            $groupedResults[$area][] = $row;
        }

        // ---------------------------------------------------------
        // 4. GENERACIÓN DE PDF (Motor DomPDF Manual)
        // ---------------------------------------------------------

        // A. Carga de la Librería
        // Ajusta la ruta relativa (../../) según tu estructura de carpetas real.
        // Debe apuntar al archivo 'autoload.inc.php' dentro de la carpeta que descargaste.
        $libPath = __DIR__ . '/../../libs/dompdf/autoload.inc.php';

        if (file_exists($libPath)) {
            require_once $libPath;

            // --- NUEVO: Detectar si es copia ---
            $isCopy = ($currentStatus === 'DELIVERED');
            // -----------------------------------

            // B. Captura del HTML (Vista)
            // Usamos Output Buffering para guardar el HTML en una variable ($html)
            // en lugar de enviarlo al navegador.
            ob_start();
            $this->render('result/clinical_report', [
                'order' => $order,
                'groupedResults' => $groupedResults,
                'page_title' => 'Informe Clínico ' . $order['code'],
                'isCopy' => $isCopy // <--- PASAMOS LA VARIABLE A LA VISTA
            ], null); // null evita cargar el layout principal (header/sidebar)
            $html = ob_get_clean();

            // C. Configuración del PDF
            // Usamos \Dompdf\Options con barra invertida porque no usamos 'use' al inicio
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true); // Permite cargar imágenes (logo, firmas)
            $options->set('defaultFont', 'Helvetica');

            // D. Renderizado
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('letter', 'portrait'); // Tamaño carta o A4
            $dompdf->render();

            // E. Salida (Descarga o Visualización)
            // "Attachment" => false abre el PDF en el navegador.
            // "Attachment" => true fuerza la descarga del archivo.
            $filename = 'Resultado_' . $order['code'] . '.pdf';
            $dompdf->stream($filename, ["Attachment" => false]);
        } else {
            // FALLBACK: Si no encuentra la librería DomPDF, muestra la versión HTML simple.
            // Esto es útil si aún no has copiado la carpeta 'libs'.
            echo "<div style='background:red;color:white;padding:10px;text-align:center'>
                    ADVERTENCIA: Librería PDF no encontrada en $libPath. Mostrando versión HTML.
                  </div>";

            // --- NUEVO: Detectar si es copia también en el fallback ---
            $isCopy = ($currentStatus === 'DELIVERED');

            $this->render('result/clinical_report', [
                'order' => $order,
                'groupedResults' => $groupedResults,
                'page_title' => 'Informe Clínico ' . $order['code'],
                'isCopy' => $isCopy // <--- PASAMOS LA VARIABLE A LA VISTA
            ], null);
        }
    }


    public function archive(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $input = json_decode(file_get_contents('php://input'), true);

        // Validamos datos
        $orderId = (int)$input['order_id'];
        $receiverName = trim($input['delivered_to'] ?? '');
        $userId = $_SESSION['user_id'] ?? 1; // El usuario logueado (Recepcionista)

        if (empty($receiverName)) {
            $this->jsonResponse(['success' => false, 'message' => 'Debe especificar quién recogió el resultado.']);
            return;
        }

        $db = (new \App\Config\Database())->getConnection();

        try {
            // Actualizamos: Estado + Quién Recogió + Quién Entregó
            $sql = "UPDATE orders SET 
                        status = 'DELIVERED', 
                        delivered_to = :dto,
                        delivered_by = :dby,
                        updated_at = NOW() -- Usamos esto como fecha de entrega
                    WHERE id = :id";

            $db->prepare($sql)->execute([
                'dto' => $receiverName,
                'dby' => $userId,
                'id'  => $orderId
            ]);

            $this->jsonResponse(['success' => true, 'message' => 'Entrega registrada correctamente.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }


    public function history(): void
    {
        $db = (new \App\Config\Database())->getConnection();

        // 1. Obtener filtros
        $startDate = $_GET['start'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate   = $_GET['end']   ?? date('Y-m-d');

        // 2. Consulta SQL
        $sql = "SELECT o.*, p.first_name, p.last_name, p.document_id, o.delivered_to, o.delivered_by
                FROM orders o 
                INNER JOIN patients p ON o.patient_id = p.id 
                WHERE o.status = 'DELIVERED' 
                  AND DATE(o.updated_at) BETWEEN :start AND :end
                ORDER BY o.updated_at DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'start' => $startDate,
            'end'   => $endDate
        ]);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 3. Renderizar vista
        $this->render('result/history_list', [
            'orders' => $orders,
            'page_title' => 'Historial de Entregas',
            'filters' => ['start' => $startDate, 'end' => $endDate]
        ]);
    }
}
