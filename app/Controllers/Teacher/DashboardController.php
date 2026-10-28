<?php

namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Helpers\Auth;
use App\Core\Database;

class DashboardController extends Controller {
    public function index(): void {
        $user = Auth::user();
        $teacher = $user['teacher'] ?? null;

        if (!$teacher) {
            die("Teacher profile record not found for logged in user.");
        }

        $teacherId = (int)$teacher['id'];
        $assignments = Teacher::getAssignedClassesAndSubjects($teacherId);

        // Extract assigned class IDs
        $classIds = array_unique(array_column($assignments, 'class_id'));

        $today = date('Y-m-d');
        $pdo = Database::getInstance();

        $studentCount = 0;
        $todayAttendance = [];

        if (!empty($classIds)) {
            $inClause = implode(',', array_map('intval', $classIds));

            $stuStmt = $pdo->query("SELECT COUNT(*) FROM students WHERE class_id IN ({$inClause}) AND status = 'active'");
            $studentCount = (int)$stuStmt->fetchColumn();

            $attSql = "
                SELECT a.*, s.full_name as student_name, s.student_code, s.preview_photo,
                       c.class_name, c.class_code
                FROM attendance a
                INNER JOIN students s ON a.student_id = s.id
                INNER JOIN classes c ON a.class_id = c.id
                WHERE a.class_id IN ({$inClause}) AND a.attendance_date = ?
                ORDER BY a.check_in_time DESC
            ";
            $attStmt = $pdo->prepare($attSql);
            $attStmt->execute([$today]);
            $todayAttendance = $attStmt->fetchAll();
        }

        $this->view('teacher.dashboard', [
            'title'           => 'Teacher Dashboard',
            'teacher'         => $teacher,
            'assignments'     => $assignments,
            'classIds'        => $classIds,
            'studentCount'    => $studentCount,
            'todayAttendance' => $todayAttendance
        ]);
    }
}
