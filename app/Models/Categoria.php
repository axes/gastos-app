<?php

namespace App\Models;

use PDO;

class Categoria
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getActive()
    {
        $sql = "SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
