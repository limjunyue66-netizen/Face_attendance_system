<?php

namespace App\Models;

use App\Core\Model;

class Teacher extends Model {
    protected static string $table = 'teachers';
    protected static string $primaryKey = 'id';

    public static function getAllWithUser(): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT t.*, u.username
            FROM teachers t
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.id DESC
        ";
        return $pdo->query($sql)->fetchAll();
    }

    public static function getAssignedClassesAndSubjects(int $teacherId): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT ta.id as assignment_id, c.id as class_id, c.class_name, c.class_code, 
                   s.id as subject_id, s.subject_name, s.subject_code
            FROM teacher_assignments ta
            INNER JOIN classes c ON ta.class_id = c.id
            INNER JOIN subjects s ON ta.subject_id = s.id
            WHERE ta.teacher_id = ?
            ORDER BY c.class_name ASC, s.subject_name ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll();
    }
}
