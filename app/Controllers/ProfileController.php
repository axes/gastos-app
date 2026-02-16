<?php

namespace App\Controllers;

use App\Core\Database;
use App\Services\AuthService;

class ProfileController
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?? Database::connect();
    }

    public function index()
    {
        $user = AuthService::user();
        if (!$user) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta pagina.';
            return;
        }

        require_once __DIR__ . '/../Views/auth/profile.php';
    }

    public function update()
    {
        $user = AuthService::user();
        if (!$user) {
            http_response_code(403);
            echo 'No tienes permiso para acceder a esta pagina.';
            return;
        }

        $data = [
            'banco' => trim($_POST['banco'] ?? ''),
            'tipo_cuenta' => trim($_POST['tipo_cuenta'] ?? ''),
            'numero_cuenta' => trim($_POST['numero_cuenta'] ?? ''),
            'titular_cuenta' => trim($_POST['titular_cuenta'] ?? ''),
            'rut_titular' => trim($_POST['rut_titular'] ?? ''),
        ];

        $stmt = $this->db->prepare(
            "UPDATE users
             SET banco = :banco,
                 tipo_cuenta = :tipo_cuenta,
                 numero_cuenta = :numero_cuenta,
                 titular_cuenta = :titular_cuenta,
                 rut_titular = :rut_titular
             WHERE id = :id"
        );
        $stmt->execute([
            'banco' => $data['banco'] !== '' ? $data['banco'] : null,
            'tipo_cuenta' => $data['tipo_cuenta'] !== '' ? $data['tipo_cuenta'] : null,
            'numero_cuenta' => $data['numero_cuenta'] !== '' ? $data['numero_cuenta'] : null,
            'titular_cuenta' => $data['titular_cuenta'] !== '' ? $data['titular_cuenta'] : null,
            'rut_titular' => $data['rut_titular'] !== '' ? $data['rut_titular'] : null,
            'id' => $user['id'],
        ]);

        $_SESSION['success'] = 'Datos de transferencia actualizados.';
        header('Location: /profile');
        exit;
    }
}
