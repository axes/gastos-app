<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\CentroCosto;

class CentroCostoController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        $model = new CentroCosto($this->db);
        $centros = $model->getAll();
        require_once __DIR__ . '/../Views/auth/centros_costo/list.php';
    }

    public function createForm()
    {
        require_once __DIR__ . '/../Views/auth/centros_costo/create.php';
    }

    public function create()
    {
        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre === '') {
            $_SESSION['error'] = "El nombre es obligatorio.";
            header("Location: /centros_costo/create");
            exit;
        }

        $model = new CentroCosto($this->db);
        $model->create($nombre);
        header("Location: /centros_costo");
        exit;
    }

    public function editForm($id)
    {
        $model = new CentroCosto($this->db);
        $centro = $model->getById($id);
        if (!$centro) {
            http_response_code(404);
            echo 'Centro de costo no encontrado';
            return;
        }

        require_once __DIR__ . '/../Views/auth/centros_costo/edit.php';
    }

    public function update($id)
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;
        $model = new CentroCosto($this->db);
        $model->update($id, $nombre, $activo);
        header("Location: /centros_costo");
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
            $model = new CentroCosto($this->db);
            
            // Obtener estado actual
            $centro = $model->getById($id);
            if (!$centro) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Centro no encontrado']);
                return;
            }

            // Cambiar estado
            $model->deactivate($id);
            
            $newStatus = $centro['activo'] ? 'inactivo' : 'activo';
            $message = 'Centro de Costo ' . htmlspecialchars($centro['nombre']) . ' está ahora ' . $newStatus;
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'activo' => !$centro['activo']
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }
}
