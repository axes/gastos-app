<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthService;
use App\Services\AuthorizationService;

class DashboardController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        $user = AuthService::user();
        $role = AuthorizationService::role();
        $stats = [];

        if ($role === 'admin') {
            $stats = $this->getAdminStats();
        } elseif ($role === 'manager') {
            $stats = $this->getManagerStats();
        } elseif ($role === 'user') {
            $stats = $this->getUserStats();
        }

        require_once __DIR__ . '/../Views/auth/dashboard.php';
    }

    private function getAdminStats(): array
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM centros_costo WHERE activo = 1");
        $stmt->execute();
        $total_centros = $stmt->fetch()['count'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM proyectos WHERE activo = 1");
        $stmt->execute();
        $total_proyectos = $stmt->fetch()['count'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM gastos WHERE activo = 1");
        $stmt->execute();
        $total_gastos = $stmt->fetch()['count'];

        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM gastos WHERE estado = 'pendiente' AND activo = 1");
        $stmt->execute();
        $gastos_pendientes = $stmt->fetch()['count'];

        return compact('total_centros', 'total_proyectos', 'total_gastos', 'gastos_pendientes');
    }

    private function getManagerStats(): array
    {
        $centroIds = AuthorizationService::getManagedCentroCostos();
        
        // Total de centros asignados
        $total_centros = count($centroIds);

        // Total de proyectos en esos centros
        $total_proyectos = 0;
        $total_gastos = 0;
        $gastos_pendientes = 0;

        if (!empty($centroIds)) {
            $placeholders = implode(',', array_fill(0, count($centroIds), '?'));
            
            // Total de proyectos
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM proyectos WHERE centro_costo_id IN ({$placeholders}) AND activo = 1"
            );
            $stmt->execute($centroIds);
            $total_proyectos = $stmt->fetch()['count'];

            // Total de gastos
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 WHERE p.centro_costo_id IN ({$placeholders}) AND g.activo = 1"
            );
            $stmt->execute($centroIds);
            $total_gastos = $stmt->fetch()['count'];

            // Gastos pendientes
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 WHERE p.centro_costo_id IN ({$placeholders}) AND g.estado = 'pendiente' AND g.activo = 1"
            );
            $stmt->execute($centroIds);
            $gastos_pendientes = $stmt->fetch()['count'];
        }

        return compact('total_centros', 'total_proyectos', 'total_gastos', 'gastos_pendientes');
    }

    private function getUserStats(): array
    {
        $user = AuthService::user();

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM gastos WHERE created_by = :user_id AND activo = 1"
        );
        $stmt->execute(['user_id' => $user['id']]);
        $my_gastos = $stmt->fetch()['count'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM gastos WHERE created_by = :user_id AND estado = 'pendiente' AND activo = 1"
        );
        $stmt->execute(['user_id' => $user['id']]);
        $my_gastos_pendientes = $stmt->fetch()['count'];

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as count FROM gastos WHERE created_by = :user_id AND estado = 'aprobado' AND activo = 1"
        );
        $stmt->execute(['user_id' => $user['id']]);
        $my_gastos_aprobados = $stmt->fetch()['count'];

        return compact('my_gastos', 'my_gastos_pendientes', 'my_gastos_aprobados');
    }
}
