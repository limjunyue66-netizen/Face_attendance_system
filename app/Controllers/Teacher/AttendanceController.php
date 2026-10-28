<?php

namespace App\Controllers\Teacher;

use App\Core\Controller;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\Student;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Core\Database;

class AttendanceController extends Controller {
    public function index(): void {
        $user = Auth::user();
        $teacher = $user['teacher'] ?? null;
        if (!$teacher) {
            die("Teacher record not found.");
        }

        $assignments = Teacher::getAssignedClassesAndSubjects((int)$teacher['id']);
        $classIds = array_unique(array_column($assignments, 'class_id'));

        $selectedClassId = (int)$this->request->query('class_id') ?: null;
        $date = $this->request->query('date');

        // Security check: teacher can only query their assigned classes
        if ($selectedClassId && !in_array($selectedClassId, $classIds)) {
            $selectedClassId = null;
        }

        $records = [];
        if (!empty($classIds)) {
            $pdo = Database::getInstance();
            $sql = "
                SELECT a.*, s.full_name as student_name, s.student_code, s.preview_photo,
                       c.class_name, c.class_code
                FROM attendance a
                INNER JOIN students s ON a.student_id = s.id
                INNER JOIN classes c ON a.class_id = c.id
                WHERE 1=1
            ";
            $params = [];

            if ($selectedClassId) {
                $sql .= " AND a.class_id = ?";
                $params[] = $selectedClassId;
            } else {
                $inClause = implode(',', array_map('intval', $classIds));
                $sql .= " AND a.class_id IN ({$inClause})";
            }

            if (!empty($date)) {
                $sql .= " AND a.attendance_date = ?";
                $params[] = $date;
            }

            $sql .= " ORDER BY a.attendance_date DESC, a.check_in_time DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $records = $stmt->fetchAll();
        }

        $this->view('teacher.attendance', [
            'title'           => 'Class Attendance Records',
            'assignments'     => $assignments,
            'records'         => $records,
            'selectedClassId' => $selectedClassId,
            'date'            => $date,
        ]);
    }

    public function classes(): void {
        $user = Auth::user();
        $teacher = $user['teacher'] ?? null;
        $assignments = Teacher::getAssignedClassesAndSubjects((int)$teacher['id']);
        $classIds = array_unique(array_column($assignments, 'class_id'));

        $students = [];
        if (!empty($classIds)) {
            $pdo = Database::getInstance();
            $inClause = implode(',', array_map('intval', $classIds));
            $stmt = $pdo->query("
                SELECT s.*, c.class_name, c.class_code 
                FROM students s
                INNER JOIN classes c ON s.class_id = c.id
                WHERE s.class_id IN ({$inClause})
                ORDER BY c.class_name ASC, s.full_name ASC
            ");
            $students = $stmt->fetchAll();
        }

        $this->view('teacher.classes', [
            'title'       => 'My Assigned Classes & Students',
            'assignments' => $assignments,
            'students'    => $students
        ]);
    }

    public function studentDetails(int $id): void {
        $user = Auth::user();
        $teacher = $user['teacher'] ?? null;
        $assignments = Teacher::getAssignedClassesAndSubjects((int)$teacher['id']);
        $classIds = array_unique(array_column($assignments, 'class_id'));

        $student = Student::getWithClass($id);
        if (!$student || !in_array((int)$student['class_id'], $classIds)) {
            Session::setFlash('error', 'Unauthorized access to student record.');
            $this->redirect('/teacher/dashboard');
        }

        $statistics = Attendance::getStudentStatistics($id);
        $records = Attendance::getFilteredRecords(null, null, $id, null, 'a.attendance_date DESC');

        $this->view('teacher.student_details', [
            'title'      => "Student Attendance - {$student['full_name']}",
            'student'    => $student,
            'statistics' => $statistics,
            'records'    => $records
        ]);
    }
}
