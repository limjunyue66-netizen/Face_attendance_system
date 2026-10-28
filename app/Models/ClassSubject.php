<?php

namespace App\Models;

use App\Core\Model;

class ClassSubject extends Model {
    protected static string $table = 'class_subjects';
    protected static string $primaryKey = 'id';

    public static function getSubjectsByClass(int $classId): array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT s.*, cs.id as class_subject_id
            FROM class_subjects cs
            INNER JOIN subjects s ON cs.subject_id = s.id
            WHERE cs.class_id = ?
            ORDER BY s.subject_name ASC
        ");
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public static function getAllAssignments(): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT cs.id, cs.class_id, cs.subject_id, cs.created_at,
                   c.class_name, c.class_code, s.subject_name, s.subject_code
            FROM class_subjects cs
            INNER JOIN classes c ON cs.class_id = c.id
            INNER JOIN subjects s ON cs.subject_id = s.id
            ORDER BY c.class_name ASC, s.subject_name ASC
        ";
        return $pdo->query($sql)->fetchAll();
    }
}
