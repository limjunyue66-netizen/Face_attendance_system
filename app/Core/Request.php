<?php

namespace App\Core;

class Request {
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private ?array $jsonBody = null;

    public function __construct() {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        // Parse JSON payload if sent with Content-Type: application/json
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->jsonBody = $decoded;
            }
        }
    }

    public function method(): string {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool {
        return $this->method() === 'POST';
    }

    public function isGet(): bool {
        return $this->method() === 'GET';
    }

    public function isAjax(): bool {
        return ($this->header('X-Requested-With') === 'XMLHttpRequest') || 
               str_contains($this->header('Accept') ?? '', 'application/json');
    }

    public function uri(): string {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        // Strip query string
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        // Normalize base subfolder if running in subdirectory
        $scriptDir = str_replace('\\', '/', dirname($this->server['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // Also strip parent project directory if accessed without /public/
        if (str_ends_with($scriptDir, '/public')) {
            $projectDir = substr($scriptDir, 0, -7);
            if ($projectDir !== '' && str_starts_with($uri, $projectDir)) {
                $uri = substr($uri, strlen($projectDir));
            }
        }

        // Also check if public folder prefix was retained
        if (str_starts_with($uri, '/public')) {
            $uri = substr($uri, 7);
        }

        // Strip index.php if present in URI (e.g., /index.php, /index.php/login)
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, 10);
        } elseif ($uri === 'index.php') {
            $uri = '';
        }

        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    public function all(): array {
        if ($this->jsonBody !== null) {
            return array_merge($this->get, $this->jsonBody);
        }
        return array_merge($this->get, $this->post);
    }

    public function input(string $key, mixed $default = null): mixed {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }
        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }
        return $default;
    }

    public function postData(?string $key = null, mixed $default = null): mixed {
        if ($this->jsonBody !== null) {
            return $key === null ? $this->jsonBody : ($this->jsonBody[$key] ?? $default);
        }
        return $key === null ? $this->post : ($this->post[$key] ?? $default);
    }

    public function query(?string $key = null, mixed $default = null): mixed {
        return $key === null ? $this->get : ($this->get[$key] ?? $default);
    }

    public function file(string $key): ?array {
        return $this->files[$key] ?? null;
    }

    public function header(string $key): ?string {
        $normalized = strtoupper(str_replace('-', '_', $key));
        if (isset($this->server['HTTP_' . $normalized])) {
            return $this->server['HTTP_' . $normalized];
        }
        if (isset($this->server[$normalized])) {
            return $this->server[$normalized];
        }
        return null;
    }

    public function ip(): string {
        return $this->server['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string {
        return $this->server['HTTP_USER_AGENT'] ?? 'Unknown';
    }
}
