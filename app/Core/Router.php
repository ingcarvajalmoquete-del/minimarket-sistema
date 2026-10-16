<?php
/**
 * Router - Front Controller Pattern
 * Mini Market Management System
 */

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, string $controller, string $action): void
    {
        $this->routes['GET'][$path] = [$controller, $action];
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->routes['POST'][$path] = [$controller, $action];
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $path = parse_url($uri, PHP_URL_PATH);
        $path = rtrim($path, '/') ?: '/';

        // Exact match
        if (isset($this->routes[$method][$path])) {
            [$controllerClass, $action] = $this->routes[$method][$path];
            $this->callController($controllerClass, $action, []);
            return;
        }

        // Pattern match (e.g. /products/edit/{id})
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controllerClass, $action] = $handler;
                $this->callController($controllerClass, $action, $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        require APP_PATH . '/Views/errors/404.php';
    }

    private function callController(string $controllerClass, string $action, array $params): void
    {
        $fullClass = "App\\Controllers\\{$controllerClass}";
        if (!class_exists($fullClass)) {
            throw new \RuntimeException("Controller {$fullClass} not found");
        }
        $controller = new $fullClass();
        if (!method_exists($controller, $action)) {
            throw new \RuntimeException("Action {$action} not found in {$fullClass}");
        }
        $controller->$action($params);
    }
}
