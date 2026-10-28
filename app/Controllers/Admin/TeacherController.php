<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Teacher;
use App\Models\User;
use App\Models\AuditLog;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\Auth;
use App\Core\Database;
use Exception;

class TeacherController extends Controller {
    public function index(): void {
        $teachers = Teacher::getAllWithUser();
        $this->view('admin.teachers.index', [
            'title'    => 'Teacher Management',
            'teachers' => $teachers
        ]);
    }

    public function create(): void {
        $this->view('admin.teachers.create', ['title' => 'Add New Teacher']);
    }

    public function store(): void {
        $validator = Validator::make($this->request->all(), [
            'teacher_code' => 'required|unique:teachers,teacher_code',
            'full_name'    => 'required|min:2',
            'email'        => 'required|email|unique:teachers,email',
            'department'   => 'required',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/admin/teachers/create');
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $username = trim((string)$this->request->input('username')) ?: strtolower($this->request->input('teacher_code'));
            $rawPassword = (string)$this->request->input('password') ?: 'teacher123';

            $userStmt = $pdo->prepare("
                INSERT INTO users (username, email, password, role, status)
                VALUES (?, ?, ?, 'teacher', 'active')
            ");
            $userStmt->execute([
                $username,
                $this->request->input('email'),
                password_hash($rawPassword, PASSWORD_DEFAULT)
            ]);
            $userId = (int) $pdo->lastInsertId();

            $tStmt = $pdo->prepare("
                INSERT INTO teachers (user_id, teacher_code, full_name, email, phone, department, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $tStmt->execute([
                $userId,
                trim((string)$this->request->input('teacher_code')),
                trim((string)$this->request->input('full_name')),
                trim((string)$this->request->input('email')),
                trim((string)$this->request->input('phone')),
                trim((string)$this->request->input('department')),
                $this->request->input('status', 'active')
            ]);
            $teacherId = (int)$pdo->lastInsertId();

            AuditLog::log('TEACHER_CREATED', Auth::id(), null, null, null, [
                'teacher_id' => $teacherId,
                'full_name'  => $this->request->input('full_name')
            ]);

            $pdo->commit();
            Session::setFlash('success', "Teacher '{$this->request->input('full_name')}' added successfully.");
            $this->redirect('/admin/teachers');
        } catch (Exception $e) {
            $pdo->rollBack();
            Session::setFlash('error', "Failed to create teacher: " . $e->getMessage());
            $this->redirect('/admin/teachers/create');
        }
    }

    public function edit(int $id): void {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            Session::setFlash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }

        $this->view('admin.teachers.edit', [
            'title'   => "Edit Teacher - {$teacher['full_name']}",
            'teacher' => $teacher
        ]);
    }

    public function update(int $id): void {
        $teacher = Teacher::find($id);
        if (!$teacher) {
            Session::setFlash('error', 'Teacher not found.');
            $this->redirect('/admin/teachers');
        }

        $validator = Validator::make($this->request->all(), [
            'teacher_code' => "required|unique:teachers,teacher_code,{$id}",
            'full_name'    => 'required|min:2',
            'email'        => "required|email|unique:teachers,email,{$id}",
            'department'   => 'required',
            'status'       => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect("/admin/teachers/{$id}/edit");
        }

        Teacher::update($id, [
            'teacher_code' => trim((string)$this->request->input('teacher_code')),
            'full_name'    => trim((string)$this->request->input('full_name')),
            'email'        => trim((string)$this->request->input('email')),
            'phone'        => trim((string)$this->request->input('phone')),
            'department'   => trim((string)$this->request->input('department')),
            'status'       => $this->request->input('status')
        ]);

        AuditLog::log('TEACHER_UPDATED', Auth::id(), null, null, null, ['teacher_id' => $id]);
        Session::setFlash('success', 'Teacher record updated successfully.');
        $this->redirect('/admin/teachers');
    }

    public function delete(int $id): void {
        $teacher = Teacher::find($id);
        if ($teacher) {
            Teacher::delete($id);
            if (!empty($teacher['user_id'])) {
                User::delete($teacher['user_id']);
            }
            AuditLog::log('TEACHER_DELETED', Auth::id(), null, null, null, ['teacher_code' => $teacher['teacher_code']]);
            Session::setFlash('success', "Teacher '{$teacher['full_name']}' deleted.");
        }
        $this->redirect('/admin/teachers');
    }
}
