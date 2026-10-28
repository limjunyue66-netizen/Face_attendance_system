<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\SchoolClass;
use App\Models\AuditLog;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\Auth;

class ClassController extends Controller {
    public function index(): void {
        $classes = SchoolClass::getWithCounts();
        $this->view('admin.classes.index', [
            'title'   => 'Class Management',
            'classes' => $classes
        ]);
    }

    public function create(): void {
        $this->view('admin.classes.create', ['title' => 'Create New Class']);
    }

    public function store(): void {
        $validator = Validator::make($this->request->all(), [
            'class_code'         => 'required|unique:classes,class_code',
            'class_name'         => 'required|min:2',
            'start_time'         => 'required|time',
            'late_grace_minutes' => 'required|numeric',
            'rest_start_time'    => 'required|time',
            'rest_end_time'      => 'required|time',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/admin/classes/create');
        }

        $id = SchoolClass::create([
            'class_code'         => trim((string)$this->request->input('class_code')),
            'class_name'         => trim((string)$this->request->input('class_name')),
            'start_time'         => $this->request->input('start_time'),
            'late_grace_minutes' => (int)$this->request->input('late_grace_minutes'),
            'rest_start_time'    => $this->request->input('rest_start_time'),
            'rest_end_time'      => $this->request->input('rest_end_time'),
            'status'             => $this->request->input('status', 'active'),
        ]);

        AuditLog::log('CLASS_CREATED', Auth::id(), null, null, null, [
            'class_id'   => $id,
            'class_name' => $this->request->input('class_name')
        ]);

        Session::setFlash('success', "Class '{$this->request->input('class_name')}' created successfully.");
        $this->redirect('/admin/classes');
    }

    public function edit(int $id): void {
        $class = SchoolClass::find($id);
        if (!$class) {
            Session::setFlash('error', 'Class not found.');
            $this->redirect('/admin/classes');
        }

        $this->view('admin.classes.edit', [
            'title' => "Edit Class - {$class['class_name']}",
            'class' => $class
        ]);
    }

    public function update(int $id): void {
        $class = SchoolClass::find($id);
        if (!$class) {
            Session::setFlash('error', 'Class not found.');
            $this->redirect('/admin/classes');
        }

        $validator = Validator::make($this->request->all(), [
            'class_code'         => "required|unique:classes,class_code,{$id}",
            'class_name'         => 'required|min:2',
            'start_time'         => 'required|time',
            'late_grace_minutes' => 'required|numeric',
            'rest_start_time'    => 'required|time',
            'rest_end_time'      => 'required|time',
            'status'             => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect("/admin/classes/{$id}/edit");
        }

        SchoolClass::update($id, [
            'class_code'         => trim((string)$this->request->input('class_code')),
            'class_name'         => trim((string)$this->request->input('class_name')),
            'start_time'         => $this->request->input('start_time'),
            'late_grace_minutes' => (int)$this->request->input('late_grace_minutes'),
            'rest_start_time'    => $this->request->input('rest_start_time'),
            'rest_end_time'      => $this->request->input('rest_end_time'),
            'status'             => $this->request->input('status')
        ]);

        AuditLog::log('CLASS_UPDATED', Auth::id(), null, null, null, ['class_id' => $id]);
        Session::setFlash('success', 'Class updated successfully.');
        $this->redirect('/admin/classes');
    }

    public function delete(int $id): void {
        $class = SchoolClass::find($id);
        if ($class) {
            SchoolClass::delete($id);
            AuditLog::log('CLASS_DELETED', Auth::id(), null, null, null, ['class_name' => $class['class_name']]);
            Session::setFlash('success', "Class '{$class['class_name']}' deleted.");
        }
        $this->redirect('/admin/classes');
    }
}
