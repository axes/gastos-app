<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class AuthService
{
    public static function attempt(string $rut, string $password): bool
    {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE rut = :rut AND estado = 'activo' LIMIT 1");
        $stmt->execute(['rut' => $rut]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public static function logout(): void
    {
        session_destroy();
    }

    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, rut, nombre, email FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }
}
