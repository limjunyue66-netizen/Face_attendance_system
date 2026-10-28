<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Attendance;
use App\Models\LeaveApplication;
use App\Models\AuditLog;
use App\Core\Database;

class DashboardController extends Controller {
    public function index(): void {
        $today = date('Y-m-d');
        $pdo = Database::getInstance();

        // 1. Metric Counts
        $totalStudents = Student::count();
        $activeStudents = Student::count("status = 'active'");
        $totalTeachers = Teacher::count();
        $totalClasses = SchoolClass::count();
        $registeredFaces = Student::count("face_registered = 1");

        // 2. Today's Attendance Metrics
        $attStmt = $pdo->prepare("
            SELECT 
                COUNT(*) as today_total,
                SUM(CASE WHEN status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
                SUM(CASE WHEN status = 'checked_out' THEN 1 ELSE 0 END) as checked_out,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count
            FROM attendance
            WHERE attendance_date = ?
        ");
        $attStmt->execute([$today]);
        $attStats = $attStmt->fetch();

        $todayTotal  = (int) ($attStats['today_total'] ?? 0);
        $checkedIn   = (int) ($attStats['checked_in'] ?? 0);
        $checkedOut  = (int) ($attStats['checked_out'] ?? 0);
        $lateCount   = (int) ($attStats['late_count'] ?? 0);

        // 3. Pending Leaves
        $pendingLeaves = LeaveApplication::count("status = 'pending'");

        // 4. Recent Attendance Records
        $recentAttendance = Attendance::getFilteredRecords($today, null, null, null, 'a.updated_at DESC');

        // 5. Recent Audit Logs
        $recentLogs = AuditLog::getFilteredLogs(null, null, null, 6);

        $this->view('admin.dashboard', [
            'title'            => 'Admin Dashboard - Face Attendance System',
            'totalStudents'    => $totalStudents,
            'activeStudents'   => $activeStudents,
            'totalTeachers'    => $totalTeachers,
            'totalClasses'     => $totalClasses,
            'registeredFaces'  => $registeredFaces,
            'todayTotal'       => $todayTotal,
            'checkedIn'        => $checkedIn,
            'checkedOut'       => $checkedOut,
            'lateCount'        => $lateCount,
            'pendingLeaves'    => $pendingLeaves,
            'recentAttendance' => array_slice($recentAttendance, 0, 7),
            'recentLogs'       => $recentLogs,
        ]);
    }
}
