<?php

namespace App\Controllers\Student;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\RestAttendance;
use App\Models\LeaveApplication;
use App\Helpers\Auth;

class DashboardController extends Controller {
    public function index(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;

        if (!$student) {
            die("Student profile not found for logged-in user.");
        }

        $studentId = (int)$student['id'];
        $today = date('Y-m-d');

        $todayAttendance = Attendance::getTodayRecord($studentId, $today);
        $todayRest = RestAttendance::getTodayRecord($studentId, $today);
        $hasApprovedLeave = LeaveApplication::hasApprovedLeaveToday($studentId, $today);
        $statistics = Attendance::getStudentStatistics($studentId);
        $recentRecords = Attendance::getFilteredRecords(null, null, $studentId, null, 'a.attendance_date DESC');

        $this->view('student.dashboard', [
            'title'            => 'Student Dashboard',
            'student'          => $student,
            'todayAttendance'  => $todayAttendance,
            'todayRest'        => $todayRest,
            'hasApprovedLeave' => $hasApprovedLeave,
            'statistics'       => $statistics,
            'recentRecords'    => array_slice($recentRecords, 0, 5),
        ]);
    }
}
