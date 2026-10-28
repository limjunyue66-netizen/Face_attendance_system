<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Models\TeacherAssignment;
use App\Models\AuditLog;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Core\Database;
use Exception;

class AssignmentController extends Controller {
    public function index(): void {
        $classSubjects = ClassSubject::getAllAssignments();
        $teacherAssignments = TeacherAssignment::getAllDetailed();
        $classes = SchoolClass::where('status', 'active', 'class_name ASC');
        $subjects = Subject::where('status', 'active', 'subject_name ASC');
        $teachers = Teacher::where('status', 'active', 'full_name ASC');

        $this->view('admin.assignments.index', [
            'title'              => 'Class & Teacher Assignments',
            'classSubjects'      => $classSubjects,
            'teacherAssignments' => $teacherAssignments,
            'classes'            => $classes,
            'subjects'           => $subjects,
            'teachers'           => $teachers,
        ]);
    }

    public function storeClassSubject(): void {
        $classId = (int)$this->request->input('class_id');
        $subjectId = (int)$this->request->input('subject_id');

        if (!$classId || !$subjectId) {
            Session::setFlash('error', 'Please select both a class and a subject.');
            $this->redirect('/admin/assignments');
        }

        $pdo = Database::getInstance();
        $chk = $pdo->prepare("SELECT COUNT(*) FROM class_subjects WHERE class_id = ? AND subject_id = ?");
        $chk->execute([$classId, $subjectId]);

        if ($chk->fetchColumn() > 0) {
            Session::setFlash('error', 'This subject is already assigned to the selected class.');
            $this->redirect('/admin/assignments');
        }

        ClassSubject::create([
            'class_id'   => $classId,
            'subject_id' => $subjectId
        ]);

        AuditLog::log('CLASS_SUBJECT_ASSIGNED', Auth::id(), null, null, null, [
            'class_id'   => $classId,
            'subject_id' => $subjectId
        ]);

        Session::setFlash('success', 'Subject successfully assigned to class.');
        $this->redirect('/admin/assignments');
    }

    public function deleteClassSubject(int $id): void {
        ClassSubject::delete($id);
        Session::setFlash('success', 'Class-subject link removed.');
        $this->redirect('/admin/assignments');
    }

    public function storeTeacherAssignment(): void {
        $teacherId = (int)$this->request->input('teacher_id');
        $classId = (int)$this->request->input('class_id');
        $subjectId = (int)$this->request->input('subject_id');

        if (!$teacherId || !$classId || !$subjectId) {
            Session::setFlash('error', 'Please select a teacher, class, and subject.');
            $this->redirect('/admin/assignments');
        }

        $pdo = Database::getInstance();
        $chk = $pdo->prepare("SELECT COUNT(*) FROM teacher_assignments WHERE teacher_id = ? AND class_id = ? AND subject_id = ?");
        $chk->execute([$teacherId, $classId, $subjectId]);

        if ($chk->fetchColumn() > 0) {
            Session::setFlash('error', 'This teacher is already assigned to this class and subject.');
            $this->redirect('/admin/assignments');
        }

        TeacherAssignment::create([
            'teacher_id' => $teacherId,
            'class_id'   => $classId,
            'subject_id' => $subjectId
        ]);

        AuditLog::log('TEACHER_ASSIGNED', Auth::id(), null, null, null, [
            'teacher_id' => $teacherId,
            'class_id'   => $classId,
            'subject_id' => $subjectId
        ]);

        Session::setFlash('success', 'Teacher assigned successfully.');
        $this->redirect('/admin/assignments');
    }

    public function deleteTeacherAssignment(int $id): void {
        TeacherAssignment::delete($id);
        Session::setFlash('success', 'Teacher assignment removed.');
        $this->redirect('/admin/assignments');
    }
}
