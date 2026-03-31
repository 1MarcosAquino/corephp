<?php

namespace App\Core;

class Response
{
    private mixed $data = null;
    private int $status = 200;
    private array $headers = [];

    public function json(mixed $data): self
    {
        $this->headers["Content-Type"] = "application/json; charset=utf-8";

        $this->data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($this->data === false) {
            http_response_code(500);
            echo json_encode([ 'success' => false, 'error' => 'Erro ao converter JSON' ]);
            exit;
        }

        return $this;
    }

    public function view($view)
    {
        $this->headers["Content-Type"] = "text/html";

        $file = __DIR__ ."/../Views/{$view}.view.html";

        if (file_exists($file)) {

            $this->data = file_get_contents($file);

            return $this;
        }

        throw new \Exception("{$view} not exists");
    }

    public function status(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    private function headers()
    {
        if (!headers_sent()) {

            foreach ($this->headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }

        http_response_code($this->status);
    }

    public function send(): void
    {
        $this->headers();

        echo $this->data;
        exit;
    }
}
