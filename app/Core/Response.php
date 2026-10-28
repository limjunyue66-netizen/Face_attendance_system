<?php

namespace App\Core;

use App\Helpers\Url;

class Response {
    public static function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public static function redirect(string $path, int $statusCode = 302): void {
        $url = (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))
            ? $path
            : Url::to($path);
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    public static function abort(int $code = 404, string $message = ''): void {
        http_response_code($code);
        $viewFile = dirname(__DIR__, 2) . "/views/errors/{$code}.php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>HTTP Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
        }
        exit;
    }
}
