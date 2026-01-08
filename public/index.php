<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../app/Config/config.php';

// Core
require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/AuthMiddleware.php';

// Services
require_once __DIR__ . '/../app/Services/AuthService.php';

// Controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';

use App\Core\Router;

$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
