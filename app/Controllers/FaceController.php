<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Student;
use App\Models\Setting;
use App\Services\FaceService;
use App\Helpers\Auth;
use Exception;

class FaceController extends Controller {
    public function getDescriptors(): void {
        $students = Student::getRegisteredStudentsWithDescriptors();
        $this->json([
            'success'  => true,
            'count'    => count($students),
            'students' => $students
        ]);
    }

    public function getSettings(): void {
        $settings = [
            'min_face_size'             => (float) Setting::get('min_face_size', 80),
            'max_face_size'             => (float) Setting::get('max_face_size', 450),
            'min_brightness'            => (float) Setting::get('min_brightness', 40),
            'max_brightness'            => (float) Setting::get('max_brightness', 220),
            'min_sharpness'             => (float) Setting::get('min_sharpness', 20),
            'detection_confidence'      => (float) Setting::get('detection_confidence', 0.50),
            'face_match_threshold'      => (float) Setting::get('face_match_threshold', 0.55),
            'min_attendance_confidence' => (float) Setting::get('min_attendance_confidence', 0.45),
            'stable_frame_count'        => (int) Setting::get('stable_frame_count', 3),
            'duplicate_scan_interval'   => (int) Setting::get('duplicate_scan_interval', 300),
            'registration_samples'      => (int) Setting::get('registration_samples', 3),
        ];

        $this->json([
            'success'  => true,
            'settings' => $settings
        ]);
    }

    public function register(): void {
        $studentId = (int) $this->request->input('student_id');
        $descriptors = $this->request->input('descriptors');
        $preview = $this->request->input('preview_image');

        if (!$studentId) {
            $this->json(['success' => false, 'message' => 'Missing student ID.'], 400);
        }

        // Authorization check: Admin can register any student, Student can only register themselves
        if (!Auth::hasRole('admin')) {
            $currentUser = Auth::user();
            if (!$currentUser || empty($currentUser['student']) || (int)$currentUser['student']['id'] !== $studentId) {
                $this->json(['success' => false, 'message' => 'Unauthorized face registration.'], 403);
            }
        }

        if (empty($descriptors) || !is_array($descriptors)) {
            $this->json(['success' => false, 'message' => 'Invalid or empty facial descriptors.'], 400);
        }

        try {
            $result = FaceService::registerFace($studentId, $descriptors, $preview, Auth::id());
            $this->json($result);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
