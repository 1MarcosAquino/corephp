<?php

namespace App\Core;

use Exception;

class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][] = [
            'pattern' => $this->compileRoute($path),
            'handler' => $handler
        ];
    }

    // syntactic sugar (opcional)
    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    private function compileRoute(string $path): string
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $path);
        return "#^" . rtrim($pattern, '/') . "$#";
    }

    public function run(): void
    {
        $request  = new Request();
        $response = new Response();

        $method = $request->method();
        $uri    = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->dispatch($route['handler'], $request, $response, $params);
                return;
            }
        }

        $response->json(['error' => 'Rota não encontrada'])
                 ->status(404)
                 ->send();
    }

    private function dispatch($handler, Request $request, Response $response, array $params): void
    {
        try {
            // Closure
            if (is_callable($handler)) {
                $result = $handler($request, $response, ...array_values($params));
            }
            // Controller [Class, method]
            elseif (is_array($handler)) {
                [$class, $method] = $handler;

                if (!class_exists($class)) {
                    throw new Exception("Controller {$class} não existe");
                }

                $controller = new $class();

                if (!method_exists($controller, $method)) {
                    throw new Exception("Método {$method} não existe");
                }

                $result = $controller->$method($request, $response, ...array_values($params));
            } else {
                throw new Exception("Handler inválido");
            }

            $this->resolveResponse($result, $response);

        } catch (Exception $e) {
            $response->json(['error' => $e->getMessage()])
                     ->status(500)
                     ->send();
        }
    }

    private function resolveResponse($result, Response $response): void
    {
        if ($result instanceof Response) {
            $result->send();
            return;
        }

        if (is_array($result)) {
            $response->json($result)->send();
            return;
        }

        if (is_string($result)) {
            $response->view($result)->send();
            return;
        }

        if ($result === null) {
            $response->status(204)->send();
        }
    }
}
