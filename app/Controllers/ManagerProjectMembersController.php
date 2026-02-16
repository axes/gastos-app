<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthService;
use App\Services\AuthorizationService;

class ManagerProjectMembersController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        if (!AuthorizationService::isManager()) {
            http_response_code(403);
            echo 'No tienes permiso.';
            return;
        }

        $managedCentros = AuthorizationService::getManagedCentroCostos();
        if (empty($managedCentros)) {
            $_SESSION['error'] = 'No tienes centros asignados.';
            header('Location: /dashboard');
            exit;
        }

        // Get proyectos in managed centros
        $placeholders = implode(',', array_fill(0, count($managedCentros), '?'));
        $stmt = $this->db->prepare(
            "SELECT p.id, p.nombre, c.nombre AS centro_costo, COUNT(pm.id) AS member_count
             FROM proyectos p
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             LEFT JOIN proyecto_members pm ON pm.proyecto_id = p.id AND pm.activo = 1
             WHERE p.centro_costo_id IN ({$placeholders})
             GROUP BY p.id
             ORDER BY p.nombre"
        );
        $stmt->execute($managedCentros);
        $proyectos = $stmt->fetchAll();

        // Get all active users for selector with departamento
        $stmt = $this->db->prepare(
            "SELECT id, nombre, departamento FROM users WHERE estado = 'activo' ORDER BY departamento, nombre"
        );
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Pasar $db a la vista
        $db = $this->db;

        require_once __DIR__ . '/../Views/auth/manager/project_members.php';
    }

    public function assign()
    {
        if (!AuthorizationService::isManager()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $proyectoId = (int)($_POST['proyecto_id'] ?? 0);
        $usersData = $_POST['users_data'] ?? [];
        $encargados = $_POST['encargados'] ?? [];

        if ($proyectoId <= 0 || empty($usersData)) {
            $_SESSION['error'] = 'Debes seleccionar un proyecto y al menos un usuario.';
            header('Location: /manager/project-members');
            exit;
        }

        // Verify manager can manage this proyecto
        if (!AuthorizationService::canManageProyecto($proyectoId)) {
            $_SESSION['error'] = 'No tienes permiso para asignar miembros en este proyecto.';
            header('Location: /manager/project-members');
            exit;
        }

        // Asegurar que todos son enteros
        $usersData = array_map('intval', $usersData);
        $encargados = array_map('intval', $encargados);

        // Validar que los usuarios existan y estén activos
        $placeholders = implode(',', array_fill(0, count($usersData), '?'));
        $stmt = $this->db->prepare("SELECT id FROM users WHERE id IN ({$placeholders}) AND estado = 'activo'");
        $stmt->execute($usersData);
        $validUsers = $stmt->fetchAll();

        if (count($validUsers) !== count($usersData)) {
            $_SESSION['error'] = 'Uno o más usuarios no son válidos.';
            header('Location: /manager/project-members');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // Asignar todos los usuarios seleccionados
            foreach ($usersData as $userId) {
                $isEncargado = in_array($userId, $encargados) ? 'encargado' : 'member';
                
                $stmt = $this->db->prepare(
                    "INSERT INTO proyecto_members (proyecto_id, user_id, role_in_project, activo)
                     VALUES (:proyecto_id, :user_id, :role, 1)
                     ON DUPLICATE KEY UPDATE role_in_project = :role, activo = 1"
                );
                $stmt->execute([
                    'proyecto_id' => $proyectoId,
                    'user_id' => $userId,
                    'role' => $isEncargado,
                ]);
            }

            $this->db->commit();
            $_SESSION['success'] = count($usersData) . ' usuario(s) asignado(s) al proyecto.';
        } catch (\Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Error al asignar miembros: ' . $e->getMessage();
        }

        header('Location: /manager/project-members');
        exit;
    }

    public function unassign($id)
    {
        if (!AuthorizationService::isManager()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $id = (int)$id;

        // Get proyecto_id from this member record
        $stmt = $this->db->prepare("SELECT proyecto_id FROM proyecto_members WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $member = $stmt->fetch();

        if (!$member || !AuthorizationService::canManageProyecto($member['proyecto_id'])) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE proyecto_members SET activo = 0 WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = 'Miembro removido del proyecto.';
        header('Location: /manager/project-members');
        exit;
    }

    public function promoteToEncargado($id)
    {
        if (!AuthorizationService::isManager()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $id = (int)$id;

        // Get proyecto_id from this member record
        $stmt = $this->db->prepare("SELECT proyecto_id FROM proyecto_members WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $member = $stmt->fetch();

        if (!$member || !AuthorizationService::canManageProyecto($member['proyecto_id'])) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE proyecto_members SET role_in_project = 'encargado' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = 'Miembro promovido a Encargado del Proyecto.';
        header('Location: /manager/project-members');
        exit;
    }

    public function demoteToMember($id)
    {
        if (!AuthorizationService::isManager()) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $id = (int)$id;

        // Get proyecto_id from this member record
        $stmt = $this->db->prepare("SELECT proyecto_id FROM proyecto_members WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $member = $stmt->fetch();

        if (!$member || !AuthorizationService::canManageProyecto($member['proyecto_id'])) {
            $_SESSION['error'] = 'No tienes permiso.';
            header('Location: /manager/project-members');
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE proyecto_members SET role_in_project = 'member' WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = 'Encargado degradado a Miembro.';
        header('Location: /manager/project-members');
        exit;
    }
}
