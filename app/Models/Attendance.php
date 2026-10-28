<?php

namespace App\Models;

use App\Core\Model;

class Attendance extends Model {
    protected static string $table = 'attendance';
    protected static string $primaryKey = 'id';

    public static function getTodayRecord(int $studentId, string $date): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM attendance WHERE student_id = ? AND attendance_date = ? LIMIT 1");
        $stmt->execute([$studentId, $date]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getFilteredRecords(
        ?string $date = null,
        ?int $classId = null,
        ?int $studentId = null,
        ?string $status = null,
        string $orderBy = 'a.attendance_date DESC, a.check_in_time DESC'
    ): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT a.*, s.full_name as student_name, s.student_code, s.preview_photo,
                   c.class_name, c.class_code
            FROM attendance a
            INNER JOIN students s ON a.student_id = s.id
            LEFT JOIN classes c ON a.class_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($date)) {
            $sql .= " AND a.attendance_date = ?";
            $params[] = $date;
        }

        if (!empty($classId)) {
            $sql .= " AND a.class_id = ?";
            $params[] = $classId;
        }

        if (!empty($studentId)) {
            $sql .= " AND a.student_id = ?";
            $params[] = $studentId;
        }

        if (!empty($status)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY {$orderBy}";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getStudentStatistics(int $studentId): array {
        $pdo = self::getPdo();
        
        $sql = "
            SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN status IN ('checked_in', 'checked_out') THEN 1 ELSE 0 END) as on_time_days,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_days,
                SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as leave_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days
            FROM attendance
            WHERE student_id = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$studentId]);
        $stat = $stmt->fetch();

        $total = (int) ($stat['total_days'] ?? 0);
        $onTime = (int) ($stat['on_time_days'] ?? 0);
        $late = (int) ($stat['late_days'] ?? 0);
        $leave = (int) ($stat['leave_days'] ?? 0);
        $absent = (int) ($stat['absent_days'] ?? 0);
        $present = $onTime + $late;

        $rate = $total > 0 ? round(($present / $total) * 100, 1) : 100.0;

        return [
            'total_days'    => $total,
            'present_days'  => $present,
            'on_time_days'  => $onTime,
            'late_days'     => $late,
            'leave_days'    => $leave,
            'absent_days'   => $absent,
            'percentage'    => $rate
        ];
    }
}
