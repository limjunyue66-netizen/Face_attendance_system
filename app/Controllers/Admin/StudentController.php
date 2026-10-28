<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\FaceRegistration;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Services\ExportService;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Core\Database;
use Exception;

class StudentController extends Controller {
    public function index(): void {
        $search = $this->request->query('search');
        $classId = (int) $this->request->query('class_id') ?: null;
        $status = $this->request->query('status');

        $students = Student::getAllWithClass($search, $classId, $status);
        $classes = SchoolClass::all('class_name ASC');

        $this->view('admin.students.index', [
            'title'    => 'Student Management',
            'students' => $students,
            'classes'  => $classes,
            'search'   => $search,
            'classId'  => $classId,
            'status'   => $status,
        ]);
    }

    public function create(): void {
        $classes = SchoolClass::where('status', 'active', 'class_name ASC');
        $this->view('admin.students.create', [
            'title'   => 'Add New Student',
            'classes' => $classes
        ]);
    }

    public function store(): void {
        $validator = Validator::make($this->request->all(), [
            'student_code' => 'required|unique:students,student_code',
            'full_name'    => 'required|min:2',
            'email'        => 'required|email|unique:students,email',
            'gender'       => 'required|in:male,female,other',
            'class_id'     => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/admin/students/create');
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            // Optional: Create associated student portal user account
            $createPortal = (bool) $this->request->input('create_portal_account', true);
            $userId = null;

            if ($createPortal) {
                $username = trim((string)$this->request->input('username')) ?: strtolower($this->request->input('student_code'));
                $rawPassword = (string)$this->request->input('password') ?: 'student123';

                // Check username uniqueness
                $existing = User::findByUsername($username);
                if ($existing) {
                    $username .= '_' . rand(100, 999);
                }

                $userStmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, role, status)
                    VALUES (?, ?, ?, 'student', 'active')
                ");
                $userStmt->execute([
                    $username,
                    $this->request->input('email'),
                    password_hash($rawPassword, PASSWORD_DEFAULT)
                ]);
                $userId = (int) $pdo->lastInsertId();
            }

            $dob = $this->request->input('date_of_birth') ?: null;

            $stuStmt = $pdo->prepare("
                INSERT INTO students (user_id, class_id, student_code, full_name, email, phone, gender, date_of_birth, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stuStmt->execute([
                $userId,
                $this->request->input('class_id'),
                trim((string)$this->request->input('student_code')),
                trim((string)$this->request->input('full_name')),
                trim((string)$this->request->input('email')),
                trim((string)$this->request->input('phone')),
                $this->request->input('gender'),
                $dob,
                $this->request->input('status', 'active')
            ]);
            $studentId = (int)$pdo->lastInsertId();

            AuditLog::log('STUDENT_CREATED', Auth::id(), $studentId, null, null, [
                'student_code' => $this->request->input('student_code'),
                'full_name'    => $this->request->input('full_name')
            ]);

            $pdo->commit();
            Session::setFlash('success', "Student '{$this->request->input('full_name')}' created successfully.");
            $this->redirect('/admin/students');
        } catch (Exception $e) {
            $pdo->rollBack();
            Session::setFlash('error', "Failed to create student: " . $e->getMessage());
            $this->redirect('/admin/students/create');
        }
    }

    public function show(int $id): void {
        $student = Student::getWithClass($id);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }

        $registrations = FaceRegistration::getStudentHistory($id);
        $statistics = Attendance::getStudentStatistics($id);
        $recentAttendance = Attendance::getFilteredRecords(null, null, $id, null, 'a.attendance_date DESC');

        $this->view('admin.students.show', [
            'title'            => "Student Details - {$student['full_name']}",
            'student'          => $student,
            'registrations'    => $registrations,
            'statistics'       => $statistics,
            'recentAttendance' => array_slice($recentAttendance, 0, 10),
        ]);
    }

    public function edit(int $id): void {
        $student = Student::find($id);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }

        $classes = SchoolClass::all('class_name ASC');
        $this->view('admin.students.edit', [
            'title'   => "Edit Student - {$student['full_name']}",
            'student' => $student,
            'classes' => $classes
        ]);
    }

    public function update(int $id): void {
        $student = Student::find($id);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }

        $validator = Validator::make($this->request->all(), [
            'student_code' => "required|unique:students,student_code,{$id}",
            'full_name'    => 'required|min:2',
            'email'        => "required|email|unique:students,email,{$id}",
            'gender'       => 'required|in:male,female,other',
            'class_id'     => 'required|numeric',
            'status'       => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect("/admin/students/{$id}/edit");
        }

        Student::update($id, [
            'student_code'  => trim((string)$this->request->input('student_code')),
            'full_name'     => trim((string)$this->request->input('full_name')),
            'email'         => trim((string)$this->request->input('email')),
            'phone'         => trim((string)$this->request->input('phone')),
            'gender'        => $this->request->input('gender'),
            'date_of_birth' => $this->request->input('date_of_birth') ?: null,
            'class_id'      => (int)$this->request->input('class_id'),
            'status'        => $this->request->input('status')
        ]);

        AuditLog::log('STUDENT_UPDATED', Auth::id(), $id, null, null, [
            'full_name' => $this->request->input('full_name')
        ]);

        Session::setFlash('success', "Student record updated successfully.");
        $this->redirect('/admin/students');
    }

    public function delete(int $id): void {
        $student = Student::find($id);
        if ($student) {
            $studentName = $student['full_name'] ?? 'Unknown';
            $studentCode = $student['student_code'] ?? '';
            $userId = !empty($student['user_id']) ? (int)$student['user_id'] : null;

            // 1. Delete student record
            Student::delete($id);

            // 2. Delete linked user account if exists
            if ($userId) {
                User::delete($userId);
            }

            // 3. Log audit action safely (student_id set to null to avoid foreign key violation on deleted student)
            AuditLog::log('STUDENT_DELETED', Auth::id(), null, null, null, [
                'student_id'   => $id,
                'full_name'    => $studentName,
                'student_code' => $studentCode
            ]);

            Session::setFlash('success', "Student '{$studentName}' has been deleted.");
        }
        $this->redirect('/admin/students');
    }

    public function faceRegister(int $id): void {
        $student = Student::getWithClass($id);
        if (!$student) {
            Session::setFlash('error', 'Student not found.');
            $this->redirect('/admin/students');
        }

        $this->view('admin.students.face_register', [
            'title'   => "Face Registration - {$student['full_name']}",
            'student' => $student
        ]);
    }

    public function export(): void {
        $search = $this->request->query('search');
        $classId = (int) $this->request->query('class_id') ?: null;
        $status = $this->request->query('status');

        $students = Student::getAllWithClass($search, $classId, $status);
        ExportService::exportStudentsCsv($students);
    }
}
