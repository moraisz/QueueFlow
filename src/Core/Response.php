<?php

namespace Src\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';
    private bool $sent = false;

    private const STATUS_TEXTS = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        422 => 'Unprocessable Entity',
        500 => 'Internal Server Error',
    ];

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
    * Set JSON response
    * @param array|object|null $data
    * @param int|null $statusCode
    * @return self
    */
    public function json(array|object|null $data, ?int $statusCode = null): self
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }

        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $this;
    }

    public function html(string $html, ?int $statusCode = null): self
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }

        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->setContent($html);

        return $this;
    }

    public function text(string $text, ?int $statusCode = null): self
    {
        if ($statusCode !== null) {
            $this->setStatusCode($statusCode);
        }

        $this->setHeader('Content-Type', 'text/plain; charset=utf-8');
        $this->setContent($text);

        return $this;
    }

    public function send(): void
    {
        if ($this->sent) {
            return;
        }

        // send status code
        http_response_code($this->statusCode);

        // send headers
        foreach ($this->headers as $name => $value) {
            header("$name: $value", true);
        }

        // send content
        echo $this->content;

        $this->sent = true;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getStatusText(): string
    {
        return self::STATUS_TEXTS[$this->statusCode] ?? 'Unknown';
    }
}
