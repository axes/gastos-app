<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../app/autoload.php';

use App\Core\Router;
use App\Services\AuthService;

/*
|--------------------------------------------------------------------------
| Configuración
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../app/Config/config.php';

/*
|--------------------------------------------------------------------------
| Autenticación global
|--------------------------------------------------------------------------
*/

// Rutas públicas (sin login)
$publicRoutes = [
    '/login',
    '/login/attempt',
    '/logout',
];

// Path actual (sin query string)
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!in_array($currentPath, $publicRoutes, true)) {
    if (!AuthService::check()) {
        header('Location: /login');
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/
$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
