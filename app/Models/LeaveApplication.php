<?php

namespace App\Models;

use App\Core\Model;

class LeaveApplication extends Model {
    protected static string $table = 'student_leave_applications';
    protected static string $primaryKey = 'id';

    public static function hasApprovedLeaveToday(int $studentId, string $date): bool {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM student_leave_applications 
            WHERE student_id = ? AND leave_date = ? AND status = 'approved'
        ");
        $stmt->execute([$studentId, $date]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getAllDetailed(?string $status = null, ?int $studentId = null): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT la.*, s.full_name as student_name, s.student_code, c.class_name,
                   u.username as reviewer_username
            FROM student_leave_applications la
            INNER JOIN students s ON la.student_id = s.id
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN users u ON la.reviewed_by_user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($status)) {
            $sql .= " AND la.status = ?";
            $params[] = $status;
        }

        if (!empty($studentId)) {
            $sql .= " AND la.student_id = ?";
            $params[] = $studentId;
        }

        $sql .= " ORDER BY la.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
