<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthService;
use App\Services\AuthorizationService;

class FinanceController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        if (!$this->canAccess()) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta pagina.';
            return;
        }

        $estado = trim($_GET['estado'] ?? 'aprobado');
        $allowedEstados = ['aprobado', 'reembolsado', 'pendiente', 'rechazado', 'anulado'];
        if (!in_array($estado, $allowedEstados, true)) {
            $estado = 'aprobado';
        }

        $stmt = $this->db->prepare(
            "SELECT g.*, p.nombre AS proyecto_nombre, c.nombre AS centro_costo_nombre,
                    cat.nombre AS categoria_nombre, mp.nombre AS medio_pago_nombre,
                    u.nombre AS created_by_nombre, u.email AS created_by_email,
                    u.banco, u.tipo_cuenta, u.numero_cuenta, u.titular_cuenta, u.rut_titular
             FROM gastos g
             INNER JOIN proyectos p ON p.id = g.proyecto_id
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             INNER JOIN categorias cat ON cat.id = g.categoria_id
             INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
             INNER JOIN users u ON u.id = g.created_by
             WHERE g.tipo = 'reembolso' AND g.estado = :estado AND g.activo = 1
             ORDER BY g.fecha DESC, g.id DESC"
        );
        $stmt->execute(['estado' => $estado]);
        $gastos = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/auth/finance/reembolsos.php';
    }

    public function markReembolsado($id)
    {
        if (!$this->canAccess()) {
            $_SESSION['error'] = 'No tienes permiso para gestionar reembolsos.';
            header('Location: /finance/reembolsos');
            exit;
        }

        $user = AuthService::user();

        $stmt = $this->db->prepare(
            "SELECT id, estado, tipo FROM gastos WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $gasto = $stmt->fetch();

        if (!$gasto) {
            http_response_code(404);
            return;
        }

        if ($gasto['tipo'] !== 'reembolso' || $gasto['estado'] !== 'aprobado') {
            $_SESSION['error'] = 'Solo puedes reembolsar gastos aprobados de tipo reembolso.';
            header('Location: /finance/reembolsos');
            exit;
        }

        $stmt = $this->db->prepare(
            "UPDATE gastos
             SET estado = 'reembolsado', reembolsado_by = :user_id, reembolsado_at = NOW()
             WHERE id = :id"
        );
        $stmt->execute(['user_id' => $user['id'], 'id' => $id]);

        $_SESSION['success'] = 'Gasto marcado como reembolsado.';
        header('Location: /finance/reembolsos');
        exit;
    }

    private function canAccess(): bool
    {
        return AuthorizationService::isAdmin() || AuthorizationService::isFinance();
    }
}
