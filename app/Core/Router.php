<?php

namespace App\Core;

class Router {
    private array $routes = [];
    private array $groupMiddlewares = [];

    public function get(string $path, string|array|callable $action, array $middlewares = []): self {
        $this->addRoute('GET', $path, $action, $middlewares);
        return $this;
    }

    public function post(string $path, string|array|callable $action, array $middlewares = []): self {
        $this->addRoute('POST', $path, $action, $middlewares);
        return $this;
    }

    public function group(array $attributes, callable $callback): void {
        $previousMiddlewares = $this->groupMiddlewares;
        if (!empty($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            $this->groupMiddlewares = array_merge($this->groupMiddlewares, $middlewares);
        }

        $callback($this);

        $this->groupMiddlewares = $previousMiddlewares;
    }

    private function addRoute(string $method, string $path, string|array|callable $action, array $middlewares = []): void {
        $allMiddlewares = array_merge($this->groupMiddlewares, $middlewares);
        $this->routes[] = [
            'method'      => $method,
            'path'        => '/' . trim($path, '/'),
            'action'      => $action,
            'middlewares' => $allMiddlewares,
        ];
    }

    public function dispatch(Request $request): void {
        $requestMethod = $request->method();
        $requestUri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->compileRoutePattern($route['path']);
            if (preg_match($pattern, $requestUri, $matches)) {
                // Extract named parameter values
                $params = array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY);

                // Run middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = is_string($middleware) ? new $middleware() : $middleware;
                    $middlewareInstance->handle($request);
                }

                // Execute action
                $this->executeAction($route['action'], $params, $request);
                return;
            }
        }

        // Route not found
        Response::abort(404, "Page not found: " . htmlspecialchars($requestUri));
    }

    private function compileRoutePattern(string $path): string {
        // Convert {param} to named regex (?P<param>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeAction(string|array|callable $action, array $params, Request $request): void {
        if (is_callable($action)) {
            call_user_func_array($action, array_values($params));
            return;
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controllerClass, $method] = explode('@', $action, 2);
            $fullClass = str_starts_with($controllerClass, 'App\\') ? $controllerClass : "App\\Controllers\\{$controllerClass}";

            if (!class_exists($fullClass)) {
                die("Controller class {$fullClass} not found.");
            }

            $controller = new $fullClass();
            if (!method_exists($controller, $method)) {
                die("Method {$method} not found in {$fullClass}.");
            }

            call_user_func_array([$controller, $method], array_values($params));
            return;
        }

        if (is_array($action)) {
            [$controller, $method] = $action;
            $instance = is_string($controller) ? new $controller() : $controller;
            call_user_func_array([$instance, $method], array_values($params));
            return;
        }

        die("Invalid route action defined.");
    }
}
