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

        if (isset($this->routes[$method][$uri])) {
            $this->dispatchToAction($this->routes[$method][$uri], []);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $route => $action) {
            if (strpos($route, '{') === false) {
                continue;
            }

            $pattern = preg_replace('/\{[^\/]+\}/', '([^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $uri, $matches)) {
                continue;
            }

            array_shift($matches);
            $this->dispatchToAction($action, $matches);
            return;
        }

        http_response_code(404);
        echo '404 - Route not found';
    }

    private function dispatchToAction($action, array $params): void
    {
        // Caso 1: Closure
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        // Caso 2: Controller@method
        if (is_string($action)) {
            [$controller, $method] = explode('@', $action, 2);
            $controllerClass = "App\\Controllers\\{$controller}";

            if (!class_exists($controllerClass)) {
                throw new \Exception("Controller {$controllerClass} no existe");
            }

            call_user_func_array([new $controllerClass, $method], $params);
            return;
        }

        throw new \Exception('Ruta mal definida');
    }
}
