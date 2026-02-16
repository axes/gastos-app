<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    public function showLogin()
    {
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login()
    {
        $rut = $_POST['rut'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        if (AuthService::attempt($rut, $password, $remember)) {
            header('Location: /dashboard');
            exit;
        }

        $error = 'Credenciales inválidas';
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function logout()
    {
        AuthService::logout();
        header('Location: /login');
        exit;
    }
}
