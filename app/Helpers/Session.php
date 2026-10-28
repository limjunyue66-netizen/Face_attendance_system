<?php

namespace App\Helpers;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session cookie settings
            $lifetime = 7200; // 2 hours
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path'     => '/',
                'domain'   => '',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function regenerate(): void {
        self::start();
        session_regenerate_id(true);
    }

    public static function setFlash(string $type, string $message): void {
        self::start();
        $_SESSION['_flash'][$type] = $message;
    }

    public static function getFlash(string $type): ?string {
        self::start();
        if (isset($_SESSION['_flash'][$type])) {
            $msg = $_SESSION['_flash'][$type];
            unset($_SESSION['_flash'][$type]);
            return $msg;
        }
        return null;
    }

    public static function hasFlash(string $type): bool {
        self::start();
        return isset($_SESSION['_flash'][$type]);
    }
}
