<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\CentroCosto;
use App\Models\Proyecto;

class ProyectoController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        $model = new Proyecto($this->db);
        $proyectos = $model->getAllWithCentroCosto();
        require_once __DIR__ . '/../Views/auth/proyectos/list.php';
    }

    public function createForm()
    {
        $centrosActivos = (new CentroCosto($this->db))->getActive();
        require_once __DIR__ . '/../Views/auth/proyectos/create.php';
    }

    public function create()
    {
        $centroCostoId = (int)($_POST['centro_costo_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '') ?: null;
        $fechaTermino = trim($_POST['fecha_termino'] ?? '') ?: null;
        $presupuestoRaw = trim($_POST['presupuesto'] ?? '');
        $presupuesto = $presupuestoRaw !== '' ? (float)$presupuestoRaw : null;

        if ($centroCostoId <= 0 || $nombre === '') {
            $_SESSION['error'] = 'Centro de costo y nombre son obligatorios.';
            header('Location: /proyectos/create');
            exit;
        }

        $model = new Proyecto($this->db);
        $model->create([
            'centro_costo_id' => $centroCostoId,
            'nombre' => $nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_termino' => $fechaTermino,
            'presupuesto' => $presupuesto,
        ]);
        header('Location: /proyectos');
        exit;
    }

    public function editForm($id)
    {
        $model = new Proyecto($this->db);
        $proyecto = $model->getById($id);
        if (!$proyecto) {
            http_response_code(404);
            echo 'Proyecto no encontrado';
            return;
        }

        $centrosActivos = (new CentroCosto($this->db))->getActive();
        require_once __DIR__ . '/../Views/auth/proyectos/edit.php';
    }

    public function update($id)
    {
        $centroCostoId = (int)($_POST['centro_costo_id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '') ?: null;
        $fechaTermino = trim($_POST['fecha_termino'] ?? '') ?: null;
        $presupuestoRaw = trim($_POST['presupuesto'] ?? '');
        $presupuesto = $presupuestoRaw !== '' ? (float)$presupuestoRaw : null;
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($centroCostoId <= 0 || $nombre === '') {
            $_SESSION['error'] = 'Centro de costo y nombre son obligatorios.';
            header('Location: /proyectos/edit/' . $id);
            exit;
        }

        $model = new Proyecto($this->db);
        $model->update($id, [
            'centro_costo_id' => $centroCostoId,
            'nombre' => $nombre,
            'fecha_inicio' => $fechaInicio,
            'fecha_termino' => $fechaTermino,
            'presupuesto' => $presupuesto,
            'activo' => $activo,
        ]);
        header('Location: /proyectos');
        exit;
    }

    public function deactivate($id)
    {
        // Establecer header JSON
        header('Content-Type: application/json');
        
        // Validar ID
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }

        try {
            $model = new Proyecto($this->db);
            
            // Obtener estado actual
            $proyecto = $model->getById($id);
            if (!$proyecto) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado']);
                return;
            }

            // Cambiar estado
            $model->toggleActive($id);
            
            $newStatus = $proyecto['activo'] ? 'inactivo' : 'activo';
            $message = 'Proyecto ' . htmlspecialchars($proyecto['nombre']) . ' está ahora ' . $newStatus;
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'activo' => !$proyecto['activo']
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }
}
