<?php

namespace App\Core;

class Request
{
    private array $params;
    private array $query;
    private array $body;
    private array $headers;
    private array $cookies;

    public function __construct(array $params = [])
    {
        $this->params  = $params;
        $this->query   = $_GET;
        $this->cookies = $_COOKIE;
        $this->headers = $this->parseHeaders();
        $this->body    = $this->parseBody();
    }

    private function parseBody(): array
    {
        $contentType = $this->header('content-type', '');

        // JSON
        if (str_contains($contentType, 'application/json')) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            return is_array($data) ? $data : [];
        }

        // Form-data / x-www-form-urlencoded
        return $_POST;
    }

    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }

        // IMPORTANTES
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }

        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $headers;
    }

    // ========================
    // ACESSO A DADOS
    // ========================

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key]
            ?? $this->query[$key]
            ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    // ========================
    // HEADERS / META
    // ========================

    public function header(string $name, $default = null)
    {
        return $this->headers[strtolower($name)] ?? $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function isJson(): bool
    {
        return str_contains($this->header('content-type', ''), 'application/json');
    }

    // ========================
    // ROUTE PARAMS
    // ========================

    public function params(): array
    {
        return $this->params;
    }

    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }
}
