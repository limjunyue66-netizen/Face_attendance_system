<?php

// Load .env if not already loaded
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        static $loaded = null;
        if ($loaded === null) {
            $loaded = [];
            $envFile = dirname(__DIR__) . '/.env';
            if (file_exists($envFile)) {
                $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    if (str_contains($line, '=')) {
                        [$k, $v] = explode('=', $line, 2);
                        $k = trim($k);
                        $v = trim($v);
                        // Strip quotes
                        if ((str_starts_with($v, '"') && str_ends_with($v, '"')) ||
                            (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
                            $v = substr($v, 1, -1);
                        }
                        $loaded[$k] = $v;
                    }
                }
            }
        }
        return $loaded[$key] ?? getenv($key) ?: $default;
    }
}

return [
    'connection' => env('DB_CONNECTION', 'mysql'),
    'host'       => env('DB_HOST', 'localhost'),
    'port'       => env('DB_PORT', '3307'),
    'database'   => env('DB_DATABASE', 'face_attendance_db'),
    'username'   => env('DB_USERNAME', 'root'),
    'password'   => env('DB_PASSWORD', ''),
    'charset'    => 'utf8mb4',
    'options'    => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
