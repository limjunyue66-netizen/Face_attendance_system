<?php

namespace App\Helpers;

use App\Core\Database;
use PDO;

class Auth {
    private static ?array $cachedUser = null;

    public static function check(): bool {
        return Session::has('user_id');
    }

    public static function id(): ?int {
        return Session::get('user_id');
    }

    public static function role(): ?string {
        return Session::get('user_role');
    }

    public static function hasRole(string|array $roles): bool {
        if (!self::check()) {
            return false;
        }
        $userRole = self::role();
        if (is_array($roles)) {
            return in_array($userRole, $roles, true);
        }
        return $userRole === $roles;
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $userId = self::id();
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            self::logout();
            return null;
        }

        // Attach profile details based on role
        if ($user['role'] === 'student') {
            $sStmt = $pdo->prepare("
                SELECT s.*, c.class_name, c.class_code, c.start_time, c.late_grace_minutes, c.rest_start_time, c.rest_end_time 
                FROM students s 
                LEFT JOIN classes c ON s.class_id = c.id 
                WHERE s.user_id = ? LIMIT 1
            ");
            $sStmt->execute([$userId]);
            $user['student'] = $sStmt->fetch() ?: null;
        } elseif ($user['role'] === 'teacher') {
            $tStmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ? LIMIT 1");
            $tStmt->execute([$userId]);
            $user['teacher'] = $tStmt->fetch() ?: null;
        }

        self::$cachedUser = $user;
        return self::$cachedUser;
    }

    public static function login(array $user): void {
        Session::regenerate();
        Session::set('user_id', (int) $user['id']);
        Session::set('user_role', $user['role']);
        Session::set('user_username', $user['username']);
        Session::set('user_email', $user['email']);
        self::$cachedUser = null;
    }

    public static function logout(): void {
        self::$cachedUser = null;
        Session::destroy();
    }
}
