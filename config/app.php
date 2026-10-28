<?php

require_once __DIR__ . '/database.php';

return [
    'name'        => env('APP_NAME', 'Face Attendance System'),
    'env'         => env('APP_ENV', 'development'),
    'debug'       => (bool) env('APP_DEBUG', true),
    'url'         => rtrim(env('APP_URL', 'http://localhost/Face_attendance_system'), '/'),
    'timezone'    => 'Asia/Kuala_Lumpur',
    'session_lifetime' => (int) env('SESSION_LIFETIME', 120),
    'max_upload_size'  => (int) env('MAX_UPLOAD_SIZE', 5242880), // 5MB
    'allowed_extensions' => explode(',', env('ALLOWED_EXTENSIONS', 'pdf,jpg,jpeg,png')),
];
