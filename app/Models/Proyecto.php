<?php

namespace App\Models;

use PDO;

class Proyecto
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllWithCentroCosto()
    {
        $sql = "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, p.fecha_inicio, p.fecha_termino, p.presupuesto,
                       c.nombre AS centro_costo_nombre
                FROM proyectos p
                INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveWithCentroCosto($includeId = null)
    {
        $sql = "SELECT p.id, p.centro_costo_id, p.nombre, p.activo, p.fecha_inicio, p.fecha_termino, p.presupuesto,
                       c.nombre AS centro_costo_nombre
                FROM proyectos p
                INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                WHERE p.activo = 1";
        $params = [];

        if ($includeId !== null) {
            $sql .= " OR p.id = :include_id";
            $params['include_id'] = $includeId;
        }

        $sql .= " ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM proyectos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO proyectos (centro_costo_id, nombre, fecha_inicio, fecha_termino, presupuesto, activo)
                VALUES (:centro_costo_id, :nombre, :fecha_inicio, :fecha_termino, :presupuesto, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":centro_costo_id", $data['centro_costo_id'], PDO::PARAM_INT);
        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(":fecha_inicio", $data['fecha_inicio']);
        $stmt->bindValue(":fecha_termino", $data['fecha_termino']);
        $stmt->bindValue(":presupuesto", $data['presupuesto']);
        return $stmt->execute();
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE proyectos
                SET centro_costo_id = :centro_costo_id,
                    nombre = :nombre,
                    fecha_inicio = :fecha_inicio,
                    fecha_termino = :fecha_termino,
                    presupuesto = :presupuesto,
                    activo = :activo
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":centro_costo_id", $data['centro_costo_id'], PDO::PARAM_INT);
        $stmt->bindValue(":nombre", $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(":fecha_inicio", $data['fecha_inicio']);
        $stmt->bindValue(":fecha_termino", $data['fecha_termino']);
        $stmt->bindValue(":presupuesto", $data['presupuesto']);
        $stmt->bindValue(":activo", $data['activo'], PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function toggleActive($id)
    {
        $sql = "UPDATE proyectos SET activo = IF(activo = 1, 0, 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
