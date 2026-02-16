<?php

namespace App\Models;

use PDO;

class CentroCosto
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM centros_costo ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive()
    {
        $sql = "SELECT * FROM centros_costo WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM centros_costo WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre)
    {
        $sql = "INSERT INTO centros_costo (nombre) VALUES (:nombre)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function update($id, $nombre, $activo)
    {
        $sql = "UPDATE centros_costo SET nombre = :nombre, activo = :activo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":nombre", $nombre, PDO::PARAM_STR);
        $stmt->bindValue(":activo", $activo, PDO::PARAM_INT);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deactivate($id)
    {
        $sql = "UPDATE centros_costo SET activo = IF(activo = 1, 0, 1) WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
