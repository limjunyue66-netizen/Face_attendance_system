<?php

namespace App\Models;

use App\Core\Model;

class RestAttendance extends Model {
    protected static string $table = 'rest_attendance';
    protected static string $primaryKey = 'id';

    public static function getTodayRecord(int $studentId, string $date): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM rest_attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1");
        $stmt->execute([$studentId, $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getFilteredRecords(?string $date = null, ?int $classId = null): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT ra.*, s.full_name as student_name, s.student_code,
                   c.class_name, c.class_code
            FROM rest_attendance ra
            INNER JOIN students s ON ra.student_id = s.id
            LEFT JOIN classes c ON ra.class_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($date)) {
            $sql .= " AND ra.attendance_date = ?";
            $params[] = $date;
        }

        if (!empty($classId)) {
            $sql .= " AND ra.class_id = ?";
            $params[] = $classId;
        }

        $sql .= " ORDER BY ra.attendance_date DESC, ra.rest_check_in_time DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
