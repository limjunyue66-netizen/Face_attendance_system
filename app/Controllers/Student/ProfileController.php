<?php

namespace App\Controllers\Student;

use App\Core\Controller;
use App\Models\FaceRegistration;
use App\Helpers\Auth;

class ProfileController extends Controller {
    public function index(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        if (!$student) {
            $this->setFlash('error', 'Student profile record could not be found.');
            $this->redirect('/student/dashboard');
            return;
        }

        $registrations = FaceRegistration::getStudentHistory((int)$student['id']);

        $this->view('student.profile', [
            'title'         => 'My Profile & Face Biometrics',
            'user'          => $user,
            'student'       => $student,
            'registrations' => $registrations
        ]);
    }

    public function faceRegister(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;

        $this->view('student.face_register', [
            'title'   => 'Register / Update My Face',
            'student' => $student
        ]);
    }
}
