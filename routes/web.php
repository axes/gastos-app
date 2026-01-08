<?php

use App\Core\Router;
use App\Core\AuthMiddleware;

$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/dashboard', function () {
    AuthMiddleware::handle();
    echo 'Dashboard (usuario autenticado)';
});
