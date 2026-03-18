<?php

namespace Core;

class Router
{
    private $routes = [];

    /**
     * @param string $method
     * @param string $uri
     * @param {Function} $handler  - A callback function to handle the event.
     * @param array $middleware
    */
    private function add(string $method, string $uri, $handler, $middleware = [])
    {
        $norm = '/' . trim($uri, '/');

        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $norm);

        $pattern = '@^' . $pattern . '$@D';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * @param string $uri
     * @param {Function} $handler  - A callback function to handle the event.
     * @param array $middleware
    */
    public function get(string $uri, $handler, array $middleware = [])
    {
        $this->add('GET', $uri, $handler, $middleware);
    }
    public function post(string $uri, $handler, array $middleware = [])
    {
        $this->add('GET', $uri, $handler, $middleware);
    }
    public function put(string $uri, $handler, array $middleware = [])
    {
        $this->add('PUT', $uri, $handler, $middleware);
    }
    public function delete(string $uri, $handler, array $middleware = [])
    {
        $this->add('DELETE', $uri, $handler, $middleware);
    }

    public function run()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $uriNormalized = '/' . trim($requestUri, '/');

        foreach ($this->routes[$requestMethod] ?? [] as $route) {

            if (preg_match($route['pattern'], $uriNormalized, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $res = $this->execHandler($route['handler'], $params);
                if ($res) {
                    echo json_encode($res) . PHP_EOL;
                }
                exit;

            }
        }
        echo json_encode('not found page') . PHP_EOL;
    }

    private function execHandler($handler, $params)
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler) and strpos($handler, '@')) {

            [$class, $method] = explode('@', $handler);

            $controller = 'Resouce\\Controllers\\'. $class;

            return call_user_func_array([new $controller(), $method], $params);
        }
    }
}
