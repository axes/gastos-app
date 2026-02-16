<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthorizationService;

class AdminManagerController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        // Only admins can manage managers
        if (!AuthorizationService::isAdmin()) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta página.';
            return;
        }

        // Get all active centros with their assigned manager (if any)
        $stmt = $this->db->prepare(
            "SELECT cc.id, cc.nombre,
                    u.id AS manager_id, u.nombre AS manager_nombre, u.email AS manager_email
             FROM centros_costo cc
             LEFT JOIN centro_costo_managers ccm ON ccm.centro_costo_id = cc.id AND ccm.activo = 1
             LEFT JOIN users u ON u.id = ccm.user_id AND u.role = 'manager' AND u.estado = 'activo'
             WHERE cc.activo = 1
             ORDER BY cc.nombre"
        );
        $stmt->execute();
        $centros = $stmt->fetchAll();

        // Get all active managers for the selector
        $stmt = $this->db->prepare(
            "SELECT id, nombre, email FROM users 
             WHERE role = 'manager' AND estado = 'activo' 
             ORDER BY nombre"
        );
        $stmt->execute();
        $managers = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/auth/admin/managers.php';
    }

    public function assign()
    {
        if (!AuthorizationService::isAdmin()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /admin/managers');
            exit;
        }

        $centroCostoId = (int)($_POST['centro_costo_id'] ?? 0);
        $managerId = (int)($_POST['manager_id'] ?? 0);

        if ($centroCostoId <= 0) {
            $_SESSION['error'] = 'Centro de Costo es requerido.';
            header('Location: /admin/managers');
            exit;
        }

        // Verify centro exists
        $stmt = $this->db->prepare("SELECT id FROM centros_costo WHERE id = :id AND activo = 1");
        $stmt->execute(['id' => $centroCostoId]);
        if (!$stmt->fetch()) {
            $_SESSION['error'] = 'Centro de Costo no válido.';
            header('Location: /admin/managers');
            exit;
        }

        // If manager_id is 0 or empty, remove assignment
        if ($managerId <= 0) {
            $stmt = $this->db->prepare(
                "UPDATE centro_costo_managers SET activo = 0 
                 WHERE centro_costo_id = :centro_costo_id"
            );
            $stmt->execute(['centro_costo_id' => $centroCostoId]);
            $_SESSION['success'] = 'Responsable removido del Centro de Costo.';
            header('Location: /admin/managers');
            exit;
        }

        // Verify manager exists and is a manager
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id = :id AND role = 'manager' AND estado = 'activo'");
        $stmt->execute(['id' => $managerId]);
        if (!$stmt->fetch()) {
            $_SESSION['error'] = 'Manager no válido.';
            header('Location: /admin/managers');
            exit;
        }

        // First, deactivate any existing assignment for this centro
        $stmt = $this->db->prepare(
            "UPDATE centro_costo_managers SET activo = 0 
             WHERE centro_costo_id = :centro_costo_id"
        );
        $stmt->execute(['centro_costo_id' => $centroCostoId]);

        // Then insert new assignment
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO centro_costo_managers (centro_costo_id, user_id, activo)
                 VALUES (:centro_costo_id, :user_id, 1)
                 ON DUPLICATE KEY UPDATE activo = 1"
            );
            $stmt->execute(['centro_costo_id' => $centroCostoId, 'user_id' => $managerId]);
            $_SESSION['success'] = 'Responsable asignado al Centro de Costo.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error al asignar responsable.';
        }

        header('Location: /admin/managers');
        exit;
    }

    public function unassign($id)
    {
        if (!AuthorizationService::isAdmin()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /admin/managers');
            exit;
        }

        $id = (int)$id;
        $stmt = $this->db->prepare(
            "UPDATE centro_costo_managers SET activo = 0 WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = 'Asignación removida.';
        header('Location: /admin/managers');
        exit;
    }
}
