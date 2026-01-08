<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    public function get(string $uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, $action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function dispatch(string $uri, string $method)
    {
        $uri = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo '404 - Route not found';
            return;
        }

        $action = $this->routes[$method][$uri];

        // Caso 1: Closure
        if (is_callable($action)) {
            call_user_func($action);
            return;
        }

        // Caso 2: Controller@method
        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $controllerClass = "App\\Controllers\\{$controller}";

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} no existe");
            }

            call_user_func([new $controllerClass, $method]);
            return;
        }

        throw new \Exception('Ruta mal definida');
    }
}
