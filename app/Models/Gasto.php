<?php

namespace App\Models;

use PDO;

class Gasto
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllWithFilters(array $filters)
    {
        $sql = "SELECT g.*, p.nombre AS proyecto_nombre, c.id AS centro_costo_id, c.nombre AS centro_costo_nombre,
                       cat.nombre AS categoria_nombre, mp.nombre AS medio_pago_nombre
                FROM gastos g
                INNER JOIN proyectos p ON p.id = g.proyecto_id
                INNER JOIN centros_costo c ON c.id = p.centro_costo_id
                INNER JOIN categorias cat ON cat.id = g.categoria_id
                INNER JOIN medios_pago mp ON mp.id = g.medio_pago_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['centro_costo_id'])) {
            $sql .= " AND c.id = :centro_costo_id";
            $params['centro_costo_id'] = (int)$filters['centro_costo_id'];
        }

        if (!empty($filters['proyecto_id'])) {
            $sql .= " AND p.id = :proyecto_id";
            $params['proyecto_id'] = (int)$filters['proyecto_id'];
        }

        if (!empty($filters['desde'])) {
            $sql .= " AND g.fecha >= :desde";
            $params['desde'] = $filters['desde'];
        }

        if (!empty($filters['hasta'])) {
            $sql .= " AND g.fecha <= :hasta";
            $params['hasta'] = $filters['hasta'];
        }

        $sql .= " ORDER BY g.fecha DESC, g.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM gastos WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO gastos (proyecto_id, fecha, monto, categoria_id, medio_pago_id, descripcion, documento, tipo, estado, created_by, activo)
            VALUES (:proyecto_id, :fecha, :monto, :categoria_id, :medio_pago_id, :descripcion, :documento, :tipo, :estado, :created_by, 1)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":proyecto_id", $data['proyecto_id'], PDO::PARAM_INT);
        $stmt->bindValue(":fecha", $data['fecha']);
        $stmt->bindValue(":monto", $data['monto']);
        $stmt->bindValue(":categoria_id", $data['categoria_id'], PDO::PARAM_INT);
        $stmt->bindValue(":medio_pago_id", $data['medio_pago_id'], PDO::PARAM_INT);
        $stmt->bindValue(":descripcion", $data['descripcion']);
        $stmt->bindValue(":documento", $data['documento']);
        $stmt->bindValue(":tipo", $data['tipo']);
        $stmt->bindValue(":estado", $data['estado']);
        $stmt->bindValue(":created_by", $data['created_by'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update($id, array $data)
    {
        $sql = "UPDATE gastos
                SET proyecto_id = :proyecto_id,
                    fecha = :fecha,
                    monto = :monto,
                    categoria_id = :categoria_id,
                    medio_pago_id = :medio_pago_id,
                    descripcion = :descripcion,
                    documento = :documento,
                    tipo = :tipo,
                    estado = :estado,
                    activo = :activo
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":proyecto_id", $data['proyecto_id'], PDO::PARAM_INT);
        $stmt->bindValue(":fecha", $data['fecha']);
        $stmt->bindValue(":monto", $data['monto']);
        $stmt->bindValue(":categoria_id", $data['categoria_id'], PDO::PARAM_INT);
        $stmt->bindValue(":medio_pago_id", $data['medio_pago_id'], PDO::PARAM_INT);
        $stmt->bindValue(":descripcion", $data['descripcion']);
        $stmt->bindValue(":documento", $data['documento']);
        $stmt->bindValue(":tipo", $data['tipo']);
        $stmt->bindValue(":estado", $data['estado']);
        $stmt->bindValue(":activo", $data['activo']);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTotalAdelantoByProyecto($proyectoId): float
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(monto), 0) AS total
             FROM gastos
             WHERE proyecto_id = :proyecto_id
               AND tipo = 'adelanto'
               AND estado != 'rechazado'
               AND activo = 1"
        );
        $stmt->execute(['proyecto_id' => $proyectoId]);
        return (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function toggleActive($id)
    {
        $sql = "UPDATE gastos SET activo = IF(activo = 1, 0, 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
