<?php

namespace App\Models;

use App\Core\Model;

class TeacherAssignment extends Model {
    protected static string $table = 'teacher_assignments';
    protected static string $primaryKey = 'id';

    public static function getAllDetailed(): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT ta.id, ta.teacher_id, ta.class_id, ta.subject_id, ta.created_at,
                   t.full_name as teacher_name, t.teacher_code,
                   c.class_name, c.class_code,
                   s.subject_name, s.subject_code
            FROM teacher_assignments ta
            INNER JOIN teachers t ON ta.teacher_id = t.id
            INNER JOIN classes c ON ta.class_id = c.id
            INNER JOIN subjects s ON ta.subject_id = s.id
            ORDER BY t.full_name ASC, c.class_name ASC, s.subject_name ASC
        ";
        return $pdo->query($sql)->fetchAll();
    }
}
