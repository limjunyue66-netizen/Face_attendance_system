<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\RestAttendance;
use App\Models\LeaveApplication;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Helpers\FileUploader;

class AttendanceService {
    public static function processScan(int $studentId, float $confidence, ?string $base64Image = null): array {
        $student = Student::getWithClass($studentId);
        if (!$student) {
            return [
                'success' => false,
                'status_code' => 404,
                'message' => 'Student record not found in system.'
            ];
        }

        if ($student['status'] !== 'active') {
            return [
                'success' => false,
                'status_code' => 403,
                'message' => 'Student account is currently inactive.'
            ];
        }

        if (!$student['face_registered']) {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'Student face has not been registered yet.'
            ];
        }

        // Verify minimum attendance confidence
        $minConfidence = (float) Setting::get('min_attendance_confidence', 0.45);
        if ($confidence < $minConfidence) {
            AuditLog::log('ATTENDANCE_REJECTED_CONFIDENCE', null, $studentId, $confidence, null, [
                'reason' => 'Confidence below minimum threshold',
                'min_required' => $minConfidence
            ]);

            return [
                'success' => false,
                'status_code' => 422,
                'message' => 'Face match confidence is too low (' . round($confidence * 100, 1) . '%).'
            ];
        }

        $today = date('Y-m-d');
        $nowTime = date('H:i:s');

        // Check if student has approved leave today
        if (LeaveApplication::hasApprovedLeaveToday($studentId, $today)) {
            return [
                'success' => false,
                'status_code' => 400,
                'message' => 'Student is on approved leave today.'
            ];
        }

        // Save attendance snapshot image if provided
        $photoPath = null;
        if (!empty($base64Image)) {
            $photoPath = FileUploader::saveBase64Image($base64Image, 'attendance');
        }

        $duplicateInterval = (int) Setting::get('duplicate_scan_interval', 300);

        // Check if within class Rest / Break period
        $isRestPeriod = false;
        if (!empty($student['rest_start_time']) && !empty($student['rest_end_time'])) {
            if ($nowTime >= $student['rest_start_time'] && $nowTime <= $student['rest_end_time']) {
                $isRestPeriod = true;
            }
        }

        if ($isRestPeriod) {
            return self::processRestAttendance($student, $today, $nowTime, $confidence, $photoPath, $duplicateInterval);
        }

        return self::processNormalAttendance($student, $today, $nowTime, $confidence, $photoPath, $duplicateInterval);
    }

    private static function processNormalAttendance(
        array $student,
        string $today,
        string $nowTime,
        float $confidence,
        ?string $photoPath,
        int $duplicateInterval
    ): array {
        $studentId = (int) $student['id'];
        $classId = !empty($student['class_id']) ? (int) $student['class_id'] : null;

        $record = Attendance::getTodayRecord($studentId, $today);

        if (!$record) {
            // First scan of today -> Check In
            $status = 'checked_in';

            // Determine if late based on class start time & late grace period
            if (!empty($student['start_time'])) {
                $startTimeStr = $student['start_time'];
                $graceMinutes = (int) ($student['late_grace_minutes'] ?? 15);
                $cutoffTimestamp = strtotime("{$today} {$startTimeStr}") + ($graceMinutes * 60);
                $currentTimestamp = strtotime("{$today} {$nowTime}");

                if ($currentTimestamp > $cutoffTimestamp) {
                    $status = 'late';
                }
            }

            $id = Attendance::create([
                'student_id'          => $studentId,
                'class_id'            => $classId,
                'attendance_date'     => $today,
                'check_in_time'       => $nowTime,
                'check_in_photo'      => $photoPath,
                'check_in_confidence' => $confidence,
                'status'              => $status,
                'remarks'             => ($status === 'late') ? 'Late Check-In' : 'On-Time Check-In'
            ]);

            AuditLog::log('ATTENDANCE_CHECK_IN', null, $studentId, $confidence, null, [
                'type' => 'check_in',
                'status' => $status,
                'time' => $nowTime,
                'attendance_id' => $id
            ]);

            $statusText = ($status === 'late') ? 'Late Check-in' : 'Check-in';

            return [
                'success'      => true,
                'type'         => 'check_in',
                'status'       => $status,
                'student_name' => $student['full_name'],
                'student_code' => $student['student_code'],
                'class_name'   => $student['class_name'] ?? 'N/A',
                'time'         => $nowTime,
                'confidence'   => round($confidence * 100, 1),
                'photo'        => $photoPath,
                'message'      => "{$statusText} successful for {$student['full_name']} at {$nowTime}."
            ];
        }

        // Student already has attendance record today
        // Check cooldown from last scan (check_in_time or check_out_time)
        $lastScanTime = $record['check_out_time'] ?? $record['check_in_time'];
        $lastTimestamp = strtotime("{$today} {$lastScanTime}");
        $nowTimestamp = strtotime("{$today} {$nowTime}");

        if (($nowTimestamp - $lastTimestamp) < $duplicateInterval) {
            $remaining = $duplicateInterval - ($nowTimestamp - $lastTimestamp);
            return [
                'success'      => false,
                'status_code'  => 429,
                'duplicate'    => true,
                'student_name' => $student['full_name'],
                'student_code' => $student['student_code'],
                'message'      => "Attendance already recorded recently for {$student['full_name']}. Please wait {$remaining} seconds before scanning again."
            ];
        }

        // If not checked out yet, record Check-Out
        $updatedStatus = ($record['status'] === 'late') ? 'late' : 'checked_out';

        Attendance::update($record['id'], [
            'check_out_time'       => $nowTime,
            'check_out_photo'      => $photoPath ?: $record['check_out_photo'],
            'check_out_confidence' => $confidence,
            'status'               => $updatedStatus
        ]);

        AuditLog::log('ATTENDANCE_CHECK_OUT', null, $studentId, $confidence, null, [
            'type' => 'check_out',
            'time' => $nowTime,
            'attendance_id' => $record['id']
        ]);

        return [
            'success'      => true,
            'type'         => 'check_out',
            'status'       => $updatedStatus,
            'student_name' => $student['full_name'],
            'student_code' => $student['student_code'],
            'class_name'   => $student['class_name'] ?? 'N/A',
            'time'         => $nowTime,
            'confidence'   => round($confidence * 100, 1),
            'photo'        => $photoPath,
            'message'      => "Check-out successful for {$student['full_name']} at {$nowTime}."
        ];
    }

    private static function processRestAttendance(
        array $student,
        string $today,
        string $nowTime,
        float $confidence,
        ?string $photoPath,
        int $duplicateInterval
    ): array {
        $studentId = (int) $student['id'];
        $classId = !empty($student['class_id']) ? (int) $student['class_id'] : null;

        $record = RestAttendance::getTodayRecord($studentId, $today);

        if (!$record) {
            // First rest scan -> Rest Check-In
            $id = RestAttendance::create([
                'student_id'               => $studentId,
                'class_id'                 => $classId,
                'attendance_date'          => $today,
                'rest_check_in_time'       => $nowTime,
                'rest_check_in_photo'      => $photoPath,
                'rest_check_in_confidence' => $confidence
            ]);

            AuditLog::log('REST_CHECK_IN', null, $studentId, $confidence, null, [
                'type' => 'rest_check_in',
                'time' => $nowTime,
                'rest_attendance_id' => $id
            ]);

            return [
                'success'      => true,
                'type'         => 'rest_check_in',
                'status'       => 'rest_break',
                'student_name' => $student['full_name'],
                'student_code' => $student['student_code'],
                'class_name'   => $student['class_name'] ?? 'N/A',
                'time'         => $nowTime,
                'confidence'   => round($confidence * 100, 1),
                'photo'        => $photoPath,
                'message'      => "Rest / Break check-in recorded for {$student['full_name']} at {$nowTime}."
            ];
        }

        // Check duplicate cooldown
        $lastScan = $record['rest_check_out_time'] ?? $record['rest_check_in_time'];
        $lastTimestamp = strtotime("{$today} {$lastScan}");
        $nowTimestamp = strtotime("{$today} {$nowTime}");

        if (($nowTimestamp - $lastTimestamp) < $duplicateInterval) {
            return [
                'success'      => false,
                'status_code'  => 429,
                'duplicate'    => true,
                'message'      => "Rest attendance already recorded recently. Please wait before scanning again."
            ];
        }

        // Rest Check-Out
        RestAttendance::update($record['id'], [
            'rest_check_out_time'       => $nowTime,
            'rest_check_out_photo'      => $photoPath ?: $record['rest_check_out_photo'],
            'rest_check_out_confidence' => $confidence
        ]);

        AuditLog::log('REST_CHECK_OUT', null, $studentId, $confidence, null, [
            'type' => 'rest_check_out',
            'time' => $nowTime,
            'rest_attendance_id' => $record['id']
        ]);

        return [
            'success'      => true,
            'type'         => 'rest_check_out',
            'status'       => 'rest_break',
            'student_name' => $student['full_name'],
            'student_code' => $student['student_code'],
            'class_name'   => $student['class_name'] ?? 'N/A',
            'time'         => $nowTime,
            'confidence'   => round($confidence * 100, 1),
            'photo'        => $photoPath,
            'message'      => "Rest / Break return check-out recorded for {$student['full_name']} at {$nowTime}."
        ];
    }
}
