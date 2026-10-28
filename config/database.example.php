<?php

/**
 * Database Configuration Template
 * 
 * Copy this file to database.php or configure database credentials via your .env file.
 */

// Load .env helper if not already defined
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
    'host'       => env('DB_HOST', '127.0.0.1'),
    'port'       => env('DB_PORT', '3306'),
    'database'   => env('DB_DATABASE', 'face_attendance_db'),
    'username'   => env('DB_USERNAME', 'db_user'),
    'password'   => env('DB_PASSWORD', 'your_db_password'),
    'charset'    => 'utf8mb4',
    'options'    => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];
