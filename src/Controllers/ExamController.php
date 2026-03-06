<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Exam;

class ExamController extends Controller
{

    public function index(): void
    {
        $model = new Exam();
        $catalog = $model->getCatalogGrouped();

        $this->render('exams/index', [
            'page_title' => 'Catálogo de Exámenes LIMS',
            'catalog'    => $catalog,
            'extra_js'   => ['assets/js/modules/exams/catalog.js']
        ]);
    }

    public function edit(int $id): void
    {
        $model = new Exam();
        $test = $model->find($id); // Método base del Core\Model
        $ranges = $model->getRanges($id);

        $this->render('pages/exams/edit', [
            'page_title' => 'Configuración de Examen',
            'test'       => $test,
            'ranges'     => $ranges
        ]);
    }

    public function update(): void
    {
        // 1. Validar que sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "exams");
            exit;
        }

        $id = (int)$_POST['id'];

        // 2. Preparar datos (solo los campos de la tabla lab_tests)
        $data = [
            'name'         => $_POST['name'],
            'abbreviation' => $_POST['abbreviation'],
            'unit'         => $_POST['unit']
        ];

        $model = new \App\Models\Exam();

        // 3. Ejecutar actualización usando el método del Model.php
        if ($model->update($id, $data)) {
            // Redirigir con éxito
            header("Location: " . BASE_URL . "exams?success=1");
        } else {
            header("Location: " . BASE_URL . "exams?error=1");
        }
        exit;
    }

    /**
     * NUEVO: Carga el formulario parcial para el modal de Greeva
     * Sustituye el método 'view' inexistente por una carga directa de buffer
     */
    public function edit_modal(int $id): void
    {
        $model = new Exam();
        $exam = $model->find($id);
        $ranges = $model->getRanges($id);

        if (!$exam) {
            echo '<div class="alert alert-danger">Error: Examen no encontrado.</div>';
            return;
        }

        // Definimos la variable para que esté disponible en el archivo incluido
        // NUEVO: Buscar si tiene fórmula
        $db = \App\Config\Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM lab_test_formulas WHERE test_id = ?");
        $stmt->execute([$id]);
        $formula = $stmt->fetch();

        $data = [
            'exam'    => $exam,
            'ranges'  => $ranges,
            'formula' => $formula // Pasamos la fórmula a la vista
        ];
        extract($data);

        // Cargamos el archivo directamente para evitar que el render() principal 
        // inyecte el header, footer o menús dentro del modal.
        $partialPath = '../src/Views/pages/exams/partials/exam_form_partial.php';

        if (file_exists($partialPath)) {
            require_once $partialPath;
        } else {
            echo "Error: No se encontró la vista parcial en: " . $partialPath;
        }
    }

    /**
     * NUEVO: Procesa la actualización asincrónica desde el modal
     */
    public function update_ajax(): void
    {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');

        $model = new \App\Models\Exam();
        $db = \App\Config\Database::getConnection(); //

        try {
            $db->beginTransaction(); //

            $id = (int)$_POST['id'];

            // 1. Datos básicos de lab_tests
            $examData = [
                'name'                  => $_POST['name'],
                'unit'                  => $_POST['unit'],
                'result_type'           => $_POST['result_type'],
                'loinc_code'            => !empty($_POST['loinc_code']) ? $_POST['loinc_code'] : null,
                'clinical_description'  => !empty($_POST['clinical_description']) ? $_POST['clinical_description'] : null,
                'clinical_significance' => !empty($_POST['clinical_significance']) ? $_POST['clinical_significance'] : null
            ];
            $model->update($id, $examData);

            // 2. Si el examen es calculado, guardar la fórmula
            if (isset($_POST['formula_expression'])) {
                // Verificamos existencia antes del REPLACE para evitar el error 1452
                $check = $db->prepare("SELECT id FROM lab_tests WHERE id = ?");
                $check->execute([$id]);

                if ($check->rowCount() > 0) {
                    $sqlFormula = "REPLACE INTO lab_test_formulas 
                       (test_id, formula_expression, clinical_utility, precision_digits, required_variables) 
                       VALUES (?, ?, ?, ?, ?)";
                    $db->prepare($sqlFormula)->execute([
                        $id,
                        $_POST['formula_expression'],
                        $_POST['clinical_utility'] ?? null,
                        (int)($_POST['precision_digits'] ?? 2),
                        $_POST['required_variables'] ?? null
                    ]);
                }
            }

            // 3. Manejo de Rangos (la lógica que ya tenemos)
            $db->prepare("DELETE FROM lab_ref_ranges WHERE test_id = ?")->execute([$id]);
            if (isset($_POST['range_min'])) {
                $stmt = $db->prepare("INSERT INTO lab_ref_ranges (test_id, gender, age_min_days, age_max_days, range_min, range_max) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($_POST['range_min'] as $i => $val) {
                    if ($val !== '' || $_POST['range_max'][$i] !== '') {
                        $stmt->execute([$id, $_POST['gender'][$i] ?? 'B', (int)$_POST['age_min'][$i], (int)$_POST['age_max'][$i], $val !== '' ? $val : null, $_POST['range_max'][$i] !== '' ? $_POST['range_max'][$i] : null]);
                    }
                }
            }

            $db->commit();
            echo json_encode(['status' => 'success', 'message' => 'Examen, rangos y fórmula actualizados.']);
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }


    public function test_formula(): void
    {
        $expression = $_POST['formula'];
        $sampleValues = $_POST['test_values']; // Array de códigos y valores de prueba

        $result = \App\Helpers\FormulaHelper::calculate($expression, $sampleValues);

        $this->jsonResponse(['result' => $result]);
    }
}
