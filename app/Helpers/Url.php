<?php

namespace App\Helpers;

class Url {
    private static ?string $baseUrl = null;

    public static function base(): string {
        if (self::$baseUrl === null) {
            $config = require __DIR__ . '/../../config/app.php';
            if (!empty($config['url'])) {
                $url = rtrim($config['url'], '/');
                // Auto-correct accidental single slash after protocol (e.g., https:/domain -> https://domain)
                if (str_starts_with($url, 'https:/') && !str_starts_with($url, 'https://')) {
                    $url = 'https://' . substr($url, 7);
                } elseif (str_starts_with($url, 'http:/') && !str_starts_with($url, 'http://')) {
                    $url = 'http://' . substr($url, 6);
                }
                self::$baseUrl = $url;
            } else {
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
                $dir = str_replace('\\', '/', dirname($scriptName));
                if (str_ends_with($dir, '/public')) {
                    $dir = substr($dir, 0, -7);
                }
                $dir = rtrim($dir, '/');
                self::$baseUrl = "{$protocol}://{$host}{$dir}";
            }
        }
        return self::$baseUrl;
    }

    public static function to(string $path = ''): string {
        $path = ltrim($path, '/');
        return self::base() . ($path !== '' ? '/' . $path : '');
    }

    public static function asset(string $path = ''): string {
        $path = ltrim($path, '/');
        $base = self::base();
        if (str_ends_with($base, '/public')) {
            return $base . '/' . $path;
        }
        return $base . '/public/' . $path;
    }
}
