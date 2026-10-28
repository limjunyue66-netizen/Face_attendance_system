<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Subject;
use App\Models\AuditLog;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\Auth;

class SubjectController extends Controller {
    public function index(): void {
        $subjects = Subject::all('subject_name ASC');
        $this->view('admin.subjects.index', [
            'title'    => 'Subject Management',
            'subjects' => $subjects
        ]);
    }

    public function create(): void {
        $this->view('admin.subjects.create', ['title' => 'Add New Subject']);
    }

    public function store(): void {
        $validator = Validator::make($this->request->all(), [
            'subject_code' => 'required|unique:subjects,subject_code',
            'subject_name' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/admin/subjects/create');
        }

        $id = Subject::create([
            'subject_code' => trim((string)$this->request->input('subject_code')),
            'subject_name' => trim((string)$this->request->input('subject_name')),
            'description'  => trim((string)$this->request->input('description')),
            'status'       => $this->request->input('status', 'active'),
        ]);

        AuditLog::log('SUBJECT_CREATED', Auth::id(), null, null, null, [
            'subject_id'   => $id,
            'subject_name' => $this->request->input('subject_name')
        ]);

        Session::setFlash('success', "Subject '{$this->request->input('subject_name')}' added successfully.");
        $this->redirect('/admin/subjects');
    }

    public function edit(int $id): void {
        $subject = Subject::find($id);
        if (!$subject) {
            Session::setFlash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
        }

        $this->view('admin.subjects.edit', [
            'title'   => "Edit Subject - {$subject['subject_name']}",
            'subject' => $subject
        ]);
    }

    public function update(int $id): void {
        $subject = Subject::find($id);
        if (!$subject) {
            Session::setFlash('error', 'Subject not found.');
            $this->redirect('/admin/subjects');
        }

        $validator = Validator::make($this->request->all(), [
            'subject_code' => "required|unique:subjects,subject_code,{$id}",
            'subject_name' => 'required|min:2',
            'status'       => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect("/admin/subjects/{$id}/edit");
        }

        Subject::update($id, [
            'subject_code' => trim((string)$this->request->input('subject_code')),
            'subject_name' => trim((string)$this->request->input('subject_name')),
            'description'  => trim((string)$this->request->input('description')),
            'status'       => $this->request->input('status')
        ]);

        AuditLog::log('SUBJECT_UPDATED', Auth::id(), null, null, null, ['subject_id' => $id]);
        Session::setFlash('success', 'Subject updated successfully.');
        $this->redirect('/admin/subjects');
    }

    public function delete(int $id): void {
        $subject = Subject::find($id);
        if ($subject) {
            Subject::delete($id);
            AuditLog::log('SUBJECT_DELETED', Auth::id(), null, null, null, ['subject_name' => $subject['subject_name']]);
            Session::setFlash('success', "Subject '{$subject['subject_name']}' deleted.");
        }
        $this->redirect('/admin/subjects');
    }
}
