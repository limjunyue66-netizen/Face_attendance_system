<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;
use App\Models\AuditLog;
use App\Helpers\Session;
use App\Helpers\Auth;

class SettingsController extends Controller {
    public function index(): void {
        $settings = Setting::getAllKeyed();
        $this->view('admin.settings.index', [
            'title'    => 'Face Recognition & System Settings',
            'settings' => $settings
        ]);
    }

    public function update(): void {
        $fields = [
            'min_face_size'             => 'Minimum face bounding box size',
            'max_face_size'             => 'Maximum face bounding box size',
            'min_brightness'            => 'Minimum average frame brightness',
            'max_brightness'            => 'Maximum average frame brightness',
            'min_sharpness'             => 'Minimum image sharpness / contrast',
            'detection_confidence'      => 'TinyFaceDetector detection score',
            'face_match_threshold'      => 'Face recognition distance threshold',
            'min_attendance_confidence' => 'Minimum match confidence percentage',
            'stable_frame_count'        => 'Consecutive stable frames',
            'duplicate_scan_interval'   => 'Duplicate scan cooldown interval (seconds)',
            'registration_samples'      => 'Face registration sample count',
            'kiosk_title'               => 'Display title on Attendance Kiosk',
            'kiosk_school_name'         => 'School name on kiosk and reports',
        ];

        $changed = [];
        foreach ($fields as $key => $desc) {
            $val = $this->request->input($key);
            if ($val !== null) {
                Setting::set($key, trim((string)$val), $desc);
                $changed[$key] = $val;
            }
        }

        AuditLog::log('SETTINGS_CHANGED', Auth::id(), null, null, null, $changed);
        Session::setFlash('success', 'System settings saved successfully.');
        $this->redirect('/admin/settings');
    }
}
