<?php

namespace App\Models;

use App\Core\Model;

class SchoolClass extends Model {
    protected static string $table = 'classes';
    protected static string $primaryKey = 'id';

    public static function getWithCounts(): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT c.*,
                   (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count,
                   (SELECT COUNT(*) FROM class_subjects cs WHERE cs.class_id = c.id) as subject_count
            FROM classes c
            ORDER BY c.class_name ASC
        ";
        return $pdo->query($sql)->fetchAll();
    }
}
