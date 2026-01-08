<?php

namespace App\Core;

use App\Services\AuthService;

class AuthMiddleware
{
    public static function handle()
    {
        if (!AuthService::check()) {
            header('Location: /login');
            exit;
        }
    }
}
