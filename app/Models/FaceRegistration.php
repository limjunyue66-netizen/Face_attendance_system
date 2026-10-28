<?php

namespace App\Models;

use App\Core\Model;

class FaceRegistration extends Model {
    protected static string $table = 'face_registrations';
    protected static string $primaryKey = 'id';

    public static function getStudentHistory(int $studentId): array {
        $pdo = self::getPdo();
        
        // 1. Try standard registered_by_user_id column
        try {
            $stmt = $pdo->prepare("
                SELECT fr.*, u.username as registered_by
                FROM face_registrations fr
                LEFT JOIN users u ON fr.registered_by_user_id = u.id
                WHERE fr.student_id = ?
                ORDER BY fr.version DESC
            ");
            $stmt->execute([$studentId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // 2. Fallback to created_by_user_id if column differs
            try {
                $stmt = $pdo->prepare("
                    SELECT fr.*, u.username as registered_by
                    FROM face_registrations fr
                    LEFT JOIN users u ON fr.created_by_user_id = u.id
                    WHERE fr.student_id = ?
                    ORDER BY fr.version DESC
                ");
                $stmt->execute([$studentId]);
                return $stmt->fetchAll();
            } catch (\PDOException $e2) {
                // 3. Fallback to basic query without join
                $stmt = $pdo->prepare("
                    SELECT fr.*
                    FROM face_registrations fr
                    WHERE fr.student_id = ?
                    ORDER BY fr.version DESC
                ");
                $stmt->execute([$studentId]);
                return $stmt->fetchAll();
            }
        }
    }
}
