<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Patient;

class PatientController extends Controller
{

    private Patient $patientModel;

    public function __construct()
    {
        parent::__construct(); // Inicializa sesión y lógica base
        $this->patientModel = new Patient();
    }

    /**
     * ------------------------------------------------------------
     * VISTAS (HTML)
     * ------------------------------------------------------------
     */

    /**
     * Renderiza la tabla principal de pacientes
     * URL: /patient
     */
    public function index(): void
    {
        $data = [
            'page_title' => 'Gestión de Pacientes',
            'user'       => $_SESSION['user'] ?? ['name' => 'Usuario']
        ];

        // JS específico para la tabla
        $extra_js = [
            'assets/js/modules/patients/index.js'
        ];

        // Render: carpeta 'patients', vista 'index'
        $this->render('patients/index', $data, 'main', [], $extra_js);
    }

    /**
     * Renderiza el formulario de creación
     * URL: /patient/create
     */
    public function create(): void
    {
        $data = ['page_title' => 'Nuevo Paciente'];

        // JS específico para el formulario
        $extra_js = ['assets/js/modules/patients/create.js'];

        // Render: carpeta 'patients', vista 'create'
        $this->render('patients/create', $data, 'main', [], $extra_js);
    }

    /**
     * ------------------------------------------------------------
     * LOGICA DE NEGOCIO (AJAX / JSON)
     * ------------------------------------------------------------
     */

    /**
     * Endpoint DataTables (Ahora inteligente: Activos o Eliminados)
     */
    /**
     * Endpoint para DataTables
     * URL: /patient/get_datatable
     */
    public function get_datatable(): void
    {
        // 1. Seguridad AJAX
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            return;
        }

        // 2. DETECCIÓN ROBUSTA DEL MODO PAPELERA
        // Usamos $_REQUEST para aceptar tanto GET como POST.
        // DataTables a veces envía "true" (string) y a veces "1" (int).
        $trashParam = $_REQUEST['trash'] ?? 'false';

        // Convertimos a booleano real
        $showTrash = ($trashParam === 'true' || $trashParam === '1' || $trashParam === true);

        // 3. SELECCIÓN DE DATOS
        if ($showTrash) {
            // SI pide papelera, traemos SOLO los eliminados (deleted_at NOT NULL)
            $patients = $this->patientModel->getAllDeleted();
        } else {
            // NO pide papelera (default), traemos activos (deleted_at IS NULL)
            $patients = $this->patientModel->getAllForList();
        }

        // 4. RESPUESTA JSON
        // Limpiamos buffer por si acaso
        if (ob_get_length()) ob_clean();

        header('Content-Type: application/json');
        echo json_encode([
            'data' => $patients,
            'debug_mode' => $showTrash ? 'Papelera' : 'Activos' // Para que veas en el JSON qué modo detectó
        ]);
        exit;
    }


    /**
     * Lógica de Restauración
     * URL: /patient/restore/{id}
     */
    public function restore(int $id): void
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(403);
            exit;
        }

        try {
            $this->patientModel->restore($id);
            $this->jsonResponse(['success' => true, 'message' => 'Paciente restaurado exitosamente.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al restaurar.'], 500);
        }
    }


    /**
     * Procesa el guardado de un nuevo paciente
     * URL: /patient/store
     */
    public function store(): void
    {
        // 1. Seguridad: Solo AJAX
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
            http_response_code(403);
            exit('Acceso Denegado');
        }

        $input = $_POST;

        // 2. Validaciones Obligatorias
        if (empty($input['document_id']) || empty($input['first_name']) || empty($input['last_name']) || empty($input['birth_date'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Complete los campos obligatorios (*).'], 400);
        }

        // 3. Validación de Negocio: Duplicados
        if ($this->patientModel->isDocumentRegistered($input['document_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'El Documento/CI ya existe en el sistema.'], 409);
        }

        try {
            // Guardar
            $newId = $this->patientModel->create($input);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Paciente registrado exitosamente.',
                'id' => $newId
            ]);
        } catch (\Exception $e) {
            // En producción: error_log($e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Error interno al guardar.'], 500);
        }
    }


    /**
     * VISTA: Formulario de Edición
     * URL: /patient/edit/{id}
     */
    public function edit(int $id): void
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            // Si no existe, redirigir o mostrar error 404
            header('Location: ' . BASE_URL . 'patient');
            exit;
        }

        $data = [
            'page_title' => 'Editar Paciente',
            'patient'    => $patient
        ];

        // Usaremos un JS similar al create, pero específico para edit si es necesario
        // O podemos reutilizar create.js si es muy simple, pero hagamos uno dedicado.
        $extra_js = ['assets/js/modules/patients/edit.js'];

        $this->render('patients/edit', $data, 'main', [], $extra_js);
    }

    /**
     * LÓGICA: Procesar actualización
     * URL: /patient/update
     */
    public function update(): void
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(403);
            exit;
        }

        $input = $_POST;
        $id = (int)$input['id'];

        // Validaciones básicas (Igual que create)
        if (empty($input['document_id']) || empty($input['first_name'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Datos incompletos.'], 400);
        }

        // TODO: Aquí podrías validar que el DNI no pertenezca a OTRO paciente (id != $id)

        try {
            $this->patientModel->update($id, $input);
            $this->jsonResponse(['success' => true, 'message' => 'Paciente actualizado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al actualizar.'], 500);
        }
    }

    /**
     * LÓGICA: Eliminado Lógico
     * URL: /patient/delete/{id}
     * Usamos POST por seguridad, aunque la URL sugiera GET
     */
    public function delete(int $id): void
    {
        // En una API REST real usaríamos DELETE method, pero por AJAX usaremos POST
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(403);
            exit;
        }

        try {
            $this->patientModel->delete($id);
            $this->jsonResponse(['success' => true, 'message' => 'Paciente eliminado correctamente.']);
        } catch (\Exception $e) {
            $this->jsonResponse(['success' => false, 'message' => 'Error al eliminar.'], 500);
        }
    }

    /**
     * VISTA: Historial Clínico
     * URL: /patient/history/{id}
     */
    public function history(int $id): void
    {
        $patient = $this->patientModel->find($id);

        if (!$patient) {
            header('Location: ' . BASE_URL . 'patient');
            exit;
        }

        $data = [
            'page_title' => 'Historial Clínico',
            'patient'    => $patient
        ];

        // Sin JS extra por ahora
        $this->render('patients/history', $data, 'main');
    }
}
