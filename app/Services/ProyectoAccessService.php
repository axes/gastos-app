<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Proyecto;
use App\Services\AuthorizationService;

class ProyectoAccessService
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function getAccessibleProyectos(): array
    {
        $proyectoIds = AuthorizationService::getAccessibleProyectos();
        if (empty($proyectoIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($proyectoIds), '?'));
        $stmt = $this->db->prepare(
            "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
             FROM proyectos p
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             WHERE p.id IN ({$placeholders})"
        );
        $stmt->execute($proyectoIds);
        return $stmt->fetchAll();
    }

    public function getAccessibleProyectosForForm($includeId = null): array
    {
        if (AuthorizationService::isAdmin()) {
            return (new Proyecto($this->db))->getActiveWithCentroCosto($includeId);
        }

        if (AuthorizationService::isManager()) {
            $centroIds = AuthorizationService::getManagedCentroCostos();
            if (empty($centroIds)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($centroIds), '?'));
            $sql = "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
                    FROM proyectos p
                    INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                    WHERE (p.activo = 1 OR p.id = ?)
                    AND p.centro_costo_id IN ({$placeholders})
                    ORDER BY p.nombre ASC";
            $stmt = $this->db->prepare($sql);
            $params = array_merge([$includeId ?? 0], $centroIds);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        // User: only proyectos where they're member
        $user = AuthService::user();
        $stmt = $this->db->prepare(
            "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, c.nombre AS centro_costo_nombre
             FROM proyectos p
             INNER JOIN centros_costo c ON c.id = p.centro_costo_id
             INNER JOIN proyecto_members pm ON pm.proyecto_id = p.id
             WHERE pm.user_id = :user_id AND pm.activo = 1 AND (p.activo = 1 OR p.id = :include_id)
             ORDER BY p.nombre ASC"
        );
        $stmt->bindValue(':user_id', $user['id']);
        $stmt->bindValue(':include_id', $includeId ?? 0, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
