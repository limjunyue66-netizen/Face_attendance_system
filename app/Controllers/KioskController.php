<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AttendanceService;
use App\Models\Setting;
use App\Models\Attendance;

class KioskController extends Controller {
    public function index(): void {
        $today = date('Y-m-d');
        $recentAttendance = Attendance::getFilteredRecords($today, null, null, null, 'a.updated_at DESC');

        $this->view('kiosk.index', [
            'title'            => Setting::get('kiosk_title', 'Attendance Kiosk'),
            'schoolName'       => Setting::get('kiosk_school_name', 'EduVision Academy'),
            'recentAttendance' => array_slice($recentAttendance, 0, 8),
        ], 'kiosk');
    }

    public function scan(): void {
        $studentId = (int) $this->request->input('student_id');
        $confidence = (float) $this->request->input('confidence');
        $photo = $this->request->input('photo');

        if (!$studentId || $confidence <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid scan parameters received.'
            ], 400);
        }

        $result = AttendanceService::processScan($studentId, $confidence, $photo);
        $statusCode = $result['status_code'] ?? ($result['success'] ? 200 : 400);

        $this->json($result, $statusCode);
    }
}
