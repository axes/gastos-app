<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class AuthService
{
    public static function attempt(string $rut, string $password, bool $remember = false): bool
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

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        self::applyRememberOption($remember);
        return true;
    }

    public static function logout(): void
    {
        self::clearSessionCookie();
        session_destroy();
    }

    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $db = Database::connect();
        $stmt = $db->prepare("SELECT id, rut, nombre, email, departamento, role, banco, tipo_cuenta, numero_cuenta, titular_cuenta, rut_titular FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    private static function applyRememberOption(bool $remember): void
    {
        $params = session_get_cookie_params();
        $lifetime = $remember ? 60 * 60 * 24 * 14 : 0;

        setcookie(session_name(), session_id(), [
            'expires' => $lifetime ? time() + $lifetime : 0,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]);
    }

    private static function clearSessionCookie(): void
    {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => 'Lax'
        ]);
    }
}
