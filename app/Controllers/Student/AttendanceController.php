<?php

namespace App\Controllers\Student;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\RestAttendance;
use App\Helpers\Auth;
use App\Core\Database;

class AttendanceController extends Controller {
    public function scan(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        $today = date('Y-m-d');

        $todayAttendance = Attendance::getTodayRecord((int)$student['id'], $today);

        $this->view('student.attendance_scan', [
            'title'           => 'Scan Face for Attendance',
            'student'         => $student,
            'todayAttendance' => $todayAttendance
        ]);
    }

    public function history(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        $studentId = (int)$student['id'];

        $date = $this->request->query('date');
        $month = $this->request->query('month'); // YYYY-MM
        $status = $this->request->query('status');

        $pdo = Database::getInstance();
        $sql = "
            SELECT a.*, c.class_name 
            FROM attendance a
            LEFT JOIN classes c ON a.class_id = c.id
            WHERE a.student_id = ?
        ";
        $params = [$studentId];

        if (!empty($date)) {
            $sql .= " AND a.attendance_date = ?";
            $params[] = $date;
        }

        if (!empty($month)) {
            $sql .= " AND a.attendance_date LIKE ?";
            $params[] = "{$month}%";
        }

        if (!empty($status)) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY a.attendance_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        $this->view('student.attendance_history', [
            'title'   => 'Attendance History',
            'student' => $student,
            'records' => $records,
            'date'    => $date,
            'month'   => $month,
            'status'  => $status,
        ]);
    }

    public function statistics(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        $studentId = (int)$student['id'];

        $statistics = Attendance::getStudentStatistics($studentId);

        // Fetch monthly attendance breakdown for the current year
        $pdo = Database::getInstance();
        $year = date('Y');
        $monthlyStmt = $pdo->prepare("
            SELECT 
                DATE_FORMAT(attendance_date, '%b') as month_name,
                DATE_FORMAT(attendance_date, '%m') as month_num,
                SUM(CASE WHEN status IN ('checked_in', 'checked_out') THEN 1 ELSE 0 END) as on_time,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave
            FROM attendance
            WHERE student_id = ? AND attendance_date LIKE ?
            GROUP BY month_num, month_name
            ORDER BY month_num ASC
        ");
        $monthlyStmt->execute([$studentId, "{$year}-%"]);
        $monthlyData = $monthlyStmt->fetchAll();

        $this->view('student.statistics', [
            'title'       => 'Attendance Statistics & Analytics',
            'student'     => $student,
            'statistics'  => $statistics,
            'monthlyData' => $monthlyData,
            'year'        => $year
        ]);
    }
}
