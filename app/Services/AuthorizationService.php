<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class AuthorizationService
{
    private static $userRoleCache = [];
    private static $managerCentrosCache = [];
    private static $projectMembersCache = [];

    /**
     * Get the role of the current authenticated user.
     */
    public static function role(): ?string
    {
        $user = AuthService::user();
        return $user['role'] ?? null;
    }

    /**
     * Check if current user is admin.
     */
    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    /**
     * Check if current user is manager.
     */
    public static function isManager(): bool
    {
        return self::role() === 'manager';
    }

    /**
     * Check if current user is a regular user.
     */
    public static function isUser(): bool
    {
        return self::role() === 'user';
    }

    /**
     * Check if current user is finance.
     */
    public static function isFinance(): bool
    {
        return self::role() === 'finance';
    }

    /**
     * Get all Centro de Costo IDs managed by current user.
     * Returns empty array if user is not a manager or is admin.
     */
    public static function getManagedCentroCostos(): array
    {
        $user = AuthService::user();
        if (!$user) {
            return [];
        }

        if (self::isAdmin()) {
            // Admin can manage all centros
            $db = Database::connect();
            $stmt = $db->prepare("SELECT id FROM centros_costo WHERE activo = 1");
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        }

        if (!self::isManager()) {
            return [];
        }

        if (!isset(self::$managerCentrosCache[$user['id']])) {
            $db = Database::connect();
            $stmt = $db->prepare(
                "SELECT centro_costo_id FROM centro_costo_managers 
                 WHERE user_id = :user_id AND activo = 1"
            );
            $stmt->execute(['user_id' => $user['id']]);
            self::$managerCentrosCache[$user['id']] = array_column(
                $stmt->fetchAll(PDO::FETCH_ASSOC),
                'centro_costo_id'
            );
        }

        return self::$managerCentrosCache[$user['id']];
    }

    /**
     * Check if current user can manage a specific Centro de Costo.
     */
    public static function canManageCentroCosto($centroCostoId): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        if (!self::isManager()) {
            return false;
        }

        return in_array($centroCostoId, self::getManagedCentroCostos());
    }

    /**
     * Get all Proyecto IDs accessible to current user.
     * - Admin: all proyectos
     * - Manager: proyectos in managed Centros
     * - User: proyectos where user is member
     */
    public static function getAccessibleProyectos(): array
    {
        $user = AuthService::user();
        if (!$user) {
            return [];
        }

        $db = Database::connect();

        if (self::isAdmin()) {
            $stmt = $db->prepare("SELECT id FROM proyectos WHERE activo = 1");
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        }

        if (self::isManager()) {
            $centros = self::getManagedCentroCostos();
            if (empty($centros)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($centros), '?'));
            $stmt = $db->prepare(
                "SELECT id FROM proyectos WHERE centro_costo_id IN ({$placeholders}) AND activo = 1"
            );
            $stmt->execute($centros);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
        }

        // User: only proyectos where they are member
        $stmt = $db->prepare(
            "SELECT proyecto_id FROM proyecto_members 
             WHERE user_id = :user_id AND activo = 1"
        );
        $stmt->execute(['user_id' => $user['id']]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'proyecto_id');
    }

    /**
     * Check if current user can manage a specific Proyecto.
     */
    public static function canManageProyecto($proyectoId): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        $db = Database::connect();

        if (self::isManager()) {
            // Check if proyecto is in a managed centro
            $stmt = $db->prepare(
                "SELECT p.centro_costo_id FROM proyectos p
                 WHERE p.id = :proyecto_id"
            );
            $stmt->execute(['proyecto_id' => $proyectoId]);
            $proyecto = $stmt->fetch(PDO::FETCH_ASSOC);

            return $proyecto && self::canManageCentroCosto($proyecto['centro_costo_id']);
        }

        return false;
    }

    /**
     * Check if current user is a member of a specific Proyecto.
     */
    public static function isMemberOfProyecto($proyectoId): bool
    {
        $user = AuthService::user();
        if (!$user) {
            return false;
        }

        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT 1 FROM proyecto_members 
             WHERE proyecto_id = :proyecto_id AND user_id = :user_id AND activo = 1"
        );
        $stmt->execute(['proyecto_id' => $proyectoId, 'user_id' => $user['id']]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if current user is an encargado of a specific Proyecto.
     */
    public static function isEncargadoOfProyecto($proyectoId): bool
    {
        $user = AuthService::user();
        if (!$user) {
            return false;
        }

        $db = Database::connect();
        $stmt = $db->prepare(
            "SELECT 1 FROM proyecto_members 
             WHERE proyecto_id = :proyecto_id AND user_id = :user_id 
             AND role_in_project = 'encargado' AND activo = 1"
        );
        $stmt->execute(['proyecto_id' => $proyectoId, 'user_id' => $user['id']]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if current user can create a gasto in a specific Proyecto.
     */
    public static function canCreateGasto($proyectoId): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        if (self::isManager()) {
            return self::canManageProyecto($proyectoId);
        }

        // User: only if member of proyecto
        return self::isMemberOfProyecto($proyectoId);
    }

    /**
     * Check if current user can review/approve a specific Gasto.
     */
    public static function canReviewGasto($gastoId): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        $db = Database::connect();
        $user = AuthService::user();

        if (!$user) {
            return false;
        }

        // Get gasto to find proyecto
        $stmt = $db->prepare(
            "SELECT proyecto_id FROM gastos WHERE id = :gasto_id"
        );
        $stmt->execute(['gasto_id' => $gastoId]);
        $gasto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$gasto) {
            return false;
        }

        // Manager can review if proyecto is in their centros
        if (self::isManager()) {
            return self::canManageProyecto($gasto['proyecto_id']);
        }

        // User can review only if encargado of the proyecto
        return self::isEncargadoOfProyecto($gasto['proyecto_id']);
    }

    /**
     * Get all Gastos accessible to current user.
     * - Admin: all
     * - Manager: gastos from proyectos in managed centros
     * - User: gastos created by user or in proyectos where user is encargado
     */
    public static function getAccessibleGastos(): array
    {
        $user = AuthService::user();
        if (!$user) {
            return [];
        }

        $db = Database::connect();

        if (self::isAdmin()) {
            $stmt = $db->prepare(
                "SELECT g.*, 
                    p.nombre AS proyecto_nombre,
                    cc.nombre AS centro_costo_nombre,
                    cat.nombre AS categoria_nombre,
                    mp.nombre AS medio_pago_nombre
                 FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 INNER JOIN centros_costo cc ON cc.id = p.centro_costo_id
                 INNER JOIN categorias cat ON cat.id = g.categoria_id
                 INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                 ORDER BY g.fecha DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (self::isFinance()) {
            $stmt = $db->prepare(
                "SELECT g.*, 
                    p.nombre AS proyecto_nombre,
                    cc.nombre AS centro_costo_nombre,
                    cat.nombre AS categoria_nombre,
                    mp.nombre AS medio_pago_nombre
                 FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 INNER JOIN centros_costo cc ON cc.id = p.centro_costo_id
                 INNER JOIN categorias cat ON cat.id = g.categoria_id
                 INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                 ORDER BY g.fecha DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (self::isManager()) {
            $centros = self::getManagedCentroCostos();
            if (empty($centros)) {
                return [];
            }
            $placeholders = implode(',', array_fill(0, count($centros), '?'));
            $stmt = $db->prepare(
                "SELECT g.*, 
                    p.nombre AS proyecto_nombre,
                    cc.nombre AS centro_costo_nombre,
                    cat.nombre AS categoria_nombre,
                    mp.nombre AS medio_pago_nombre
                 FROM gastos g
                 INNER JOIN proyectos p ON p.id = g.proyecto_id
                 INNER JOIN centros_costo cc ON cc.id = p.centro_costo_id
                 INNER JOIN categorias cat ON cat.id = g.categoria_id
                 INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                 WHERE p.centro_costo_id IN ({$placeholders})
                 ORDER BY g.fecha DESC"
            );
            $stmt->execute($centros);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // User: only gastos created by them or in proyectos where they're encargado
        $stmt = $db->prepare(
            "SELECT g.*, 
                p.nombre AS proyecto_nombre,
                cc.nombre AS centro_costo_nombre,
                cat.nombre AS categoria_nombre,
                mp.nombre AS medio_pago_nombre
             FROM gastos g
             INNER JOIN proyectos p ON p.id = g.proyecto_id
             INNER JOIN centros_costo cc ON cc.id = p.centro_costo_id
             INNER JOIN categorias cat ON cat.id = g.categoria_id
             INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
             LEFT JOIN proyecto_members pm ON pm.proyecto_id = g.proyecto_id 
                AND pm.user_id = :user_id AND pm.role_in_project = 'encargado' AND pm.activo = 1
             WHERE g.created_by = :user_id OR pm.id IS NOT NULL
             ORDER BY g.fecha DESC"
        );
        $stmt->execute(['user_id' => $user['id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
