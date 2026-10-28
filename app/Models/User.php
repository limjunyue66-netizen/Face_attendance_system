<?php

namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';

    public static function findByUsername(string $identifier): ?array {
        $identifier = trim($identifier);
        $clean = str_replace(' ', '', $identifier);
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT u.* FROM users u
            LEFT JOIN students s ON s.user_id = u.id
            LEFT JOIN teachers t ON t.user_id = u.id
            WHERE u.username = ? 
               OR u.email = ?
               OR LOWER(u.username) = LOWER(?)
               OR LOWER(REPLACE(u.username, ' ', '')) = LOWER(?)
               OR LOWER(s.full_name) = LOWER(?)
               OR LOWER(s.student_code) = LOWER(?)
               OR LOWER(t.full_name) = LOWER(?)
               OR LOWER(t.teacher_code) = LOWER(?)
            LIMIT 1
        ");
        $stmt->execute([
            $identifier,
            $identifier,
            $identifier,
            $clean,
            $identifier,
            $identifier,
            $identifier,
            $identifier
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
