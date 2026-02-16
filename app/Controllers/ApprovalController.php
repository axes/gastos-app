<?php

namespace App\Controllers;

use App\Core\Database;
use App\Models\Gasto;
use App\Services\AuthService;
use App\Services\AuthorizationService;

class ApprovalController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        $user = AuthService::user();

        // Only manager and encargado users can see pending gastos
        if (!AuthorizationService::isManager() && !$this->hasEncargadoRole()) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta página.';
            return;
        }

        // Get gastos pending approval in user's scope
        $gastos = $this->getPendingGastosForUser();

        require_once __DIR__ . '/../Views/auth/approval/list.php';
    }

    public function approve($id)
    {
        if (!$this->canReviews()) {
            $_SESSION['error'] = 'No tienes permiso para aprobar gastos.';
            header('Location: /gastos');
            exit;
        }

        $gasto = (new Gasto($this->db))->getById($id);
        if (!$gasto) {
            http_response_code(404);
            return;
        }

        if (!AuthorizationService::canReviewGasto($id)) {
            $_SESSION['error'] = 'No tienes permiso para revisar este gasto.';
            header('Location: /approve/gastos');
            exit;
        }

        $user = AuthService::user();
        $comment = trim($_POST['comment'] ?? '');

        $stmt = $this->db->prepare(
            "UPDATE gastos 
             SET estado = 'aprobado', reviewed_by = :reviewed_by, reviewed_at = NOW(), review_comment = :comment
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'reviewed_by' => $user['id'],
            'comment' => $comment,
        ]);

        $_SESSION['success'] = 'Gasto aprobado exitosamente.';
        header('Location: /approve/gastos');
        exit;
    }

    public function reject($id)
    {
        if (!$this->canReviews()) {
            $_SESSION['error'] = 'No tienes permiso para rechazar gastos.';
            header('Location: /gastos');
            exit;
        }

        $gasto = (new Gasto($this->db))->getById($id);
        if (!$gasto) {
            http_response_code(404);
            return;
        }

        if (!AuthorizationService::canReviewGasto($id)) {
            $_SESSION['error'] = 'No tienes permiso para revisar este gasto.';
            header('Location: /approve/gastos');
            exit;
        }

        $user = AuthService::user();
        $comment = trim($_POST['comment'] ?? '');

        if ($comment === '') {
            $_SESSION['error'] = 'Debes agregar un comentario de rechazo.';
            header('Location: /approve/gastos');
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE gastos 
             SET estado = 'rechazado', reviewed_by = :reviewed_by, reviewed_at = NOW(), review_comment = :comment
             WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'reviewed_by' => $user['id'],
            'comment' => $comment,
        ]);

        $_SESSION['success'] = 'Gasto rechazado exitosamente.';
        header('Location: /approve/gastos');
        exit;
    }

    private function canReviews(): bool
    {
        return AuthorizationService::isAdmin() || 
               AuthorizationService::isManager() || 
               $this->hasEncargadoRole();
    }

    private function hasEncargadoRole(): bool
    {
        $user = AuthService::user();
        if (!$user) {
            return false;
        }

        // Check if user is encargado in any proyecto
        $stmt = $this->db->prepare(
            "SELECT 1 FROM proyecto_members 
             WHERE user_id = :user_id AND role_in_project = 'encargado' AND activo = 1 LIMIT 1"
        );
        $stmt->execute(['user_id' => $user['id']]);
        return $stmt->fetch() !== false;
    }

    private function getPendingGastosForUser(): array
    {
        $user = AuthService::user();

        if (AuthorizationService::isAdmin()) {
            $stmt = $this->db->prepare(
                "SELECT g.*, p.nombre AS proyecto_nombre, c.id AS centro_costo_id, c.nombre AS centro_costo_nombre,
                        cat.nombre AS categoria_nombre, mp.nombre AS medio_pago_nombre,
                        u.nombre AS created_by_nombre
                 FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                 INNER JOIN categorias cat ON cat.id = g.categoria_id
                 INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                 INNER JOIN users u ON u.id = g.created_by
                 WHERE g.estado = 'pendiente' AND g.activo = 1
                 ORDER BY g.fecha DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll();
        }

        if (AuthorizationService::isManager()) {
            $centroIds = AuthorizationService::getManagedCentroCostos();
            if (empty($centroIds)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($centroIds), '?'));
            $stmt = $this->db->prepare(
                "SELECT g.*, p.nombre AS proyecto_nombre, c.id AS centro_costo_id, c.nombre AS centro_costo_nombre,
                        cat.nombre AS categoria_nombre, mp.nombre AS medio_pago_nombre,
                        u.nombre AS created_by_nombre
                 FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                 INNER JOIN categorias cat ON cat.id = g.categoria_id
                 INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                 INNER JOIN users u ON u.id = g.created_by
                 WHERE p.centro_costo_id IN ({$placeholders}) AND g.estado = 'pendiente' AND g.activo = 1
                 ORDER BY g.fecha DESC"
            );
            $stmt->execute($centroIds);
            return $stmt->fetchAll();
        }

        // Encargado: only gastos from proyectos where they're encargado
        $stmt = $this->db->prepare(
            "SELECT g.*, p.nombre AS proyecto_nombre, c.id AS centro_costo_id, c.nombre AS centro_costo_nombre,
                    cat.nombre AS categoria_nombre, mp.nombre AS medio_pago_nombre,
                    u.nombre AS created_by_nombre
             FROM gastos g
             INNER JOIN proyectos p ON p.id = g.proyecto_id
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             INNER JOIN categorias cat ON cat.id = g.categoria_id
             INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
             INNER JOIN users u ON u.id = g.created_by
             INNER JOIN proyecto_members pm ON pm.proyecto_id = p.id
             WHERE pm.user_id = :user_id AND pm.role_in_project = 'encargado' AND pm.activo = 1
             AND g.estado = 'pendiente' AND g.activo = 1
             ORDER BY g.fecha DESC"
        );
        $stmt->execute(['user_id' => $user['id']]);
        return $stmt->fetchAll();
    }
}
