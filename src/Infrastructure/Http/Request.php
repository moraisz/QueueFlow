<?php

namespace Src\Infrastructure\Http;

class Request {
    private array $params = [];
    private array $query = [];
    private array $body = [];
    private array $server = [];
    private string $method = '';
    private string $path = '';
    
    public static function createFromGlobals(): self {
        $request = new self();
        
        $request->server = $_SERVER;
        $request->query = $_GET;
        $request->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $request->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // Processa o body de acordo com o Content-Type
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (str_contains($contentType, 'application/json')) {
            $json = file_get_contents('php://input');
            $request->body = json_decode($json, true) ?? [];
        } elseif ($request->method === 'POST') {
            $request->body = $_POST;
        } else {
            $json = file_get_contents('php://input');
            $decoded = json_decode($json, true);
            $request->body = $decoded ?? [];
        }
        
        return $request;
    }
    /**
     * @param array<int,mixed> $params
     */
    public function setParams(array $params): void {
        $this->params = $params;
    }
    
    public function getParams(): array {
        return $this->params;
    }
    
    public function getParam(string $key, mixed $default = null): mixed {
        return $this->params[$key] ?? $default;
    }
    
    public function getQuery(?string $key = null, mixed $default = null): mixed {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }
    
    public function getBody(?string $key = null, mixed $default = null): mixed {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }
    
    public function getMethod(): string {
        return $this->method;
    }
    
    public function getPath(): string {
        return $this->path;
    }
    
    public function getHeader(string $name): ?string {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$key] ?? null;
    }
    
    public function isJson(): bool {
        return str_contains($this->getHeader('Content-Type') ?? '', 'application/json');
    }
}
