<?php

namespace App\Controllers;

use App\Core\Controller;

class PrivacyController extends Controller {
    public function index(): void {
        $this->view('privacy.index', [
            'title' => 'Biometric Data & Privacy Policy - Face Attendance System'
        ]);
    }
}
