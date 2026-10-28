<?php

namespace App\Controllers\Student;

use App\Core\Controller;
use App\Models\LeaveApplication;
use App\Models\AuditLog;
use App\Helpers\Auth;
use App\Helpers\Validator;
use App\Helpers\Session;
use App\Helpers\FileUploader;
use Exception;

class LeaveController extends Controller {
    public function index(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        $studentId = (int)$student['id'];

        $leaves = LeaveApplication::getAllDetailed(null, $studentId);

        $this->view('student.leave_apply', [
            'title'   => 'Leave Applications',
            'student' => $student,
            'leaves'  => $leaves
        ]);
    }

    public function store(): void {
        $user = Auth::user();
        $student = $user['student'] ?? null;
        $studentId = (int)$student['id'];

        $validator = Validator::make($this->request->all(), [
            'leave_date' => 'required|date',
            'reason'     => 'required|min:5'
        ]);

        if ($validator->fails()) {
            Session::setFlash('error', $validator->firstError());
            $this->redirect('/student/leaves');
        }

        $docPath = null;
        $origName = null;

        $file = $this->request->file('document');
        if (!empty($file) && !empty($file['name'])) {
            try {
                $uploadResult = FileUploader::upload($file, 'leaves', ['pdf', 'jpg', 'jpeg', 'png'], 5242880);
                $docPath = $uploadResult['relative_path'];
                $origName = $uploadResult['original_name'];
            } catch (Exception $e) {
                Session::setFlash('error', "Document upload failed: " . $e->getMessage());
                $this->redirect('/student/leaves');
            }
        }

        $id = LeaveApplication::create([
            'student_id'        => $studentId,
            'leave_date'        => $this->request->input('leave_date'),
            'reason'            => trim((string)$this->request->input('reason')),
            'document_path'     => $docPath,
            'original_filename' => $origName,
            'status'            => 'pending'
        ]);

        AuditLog::log('LEAVE_SUBMITTED', Auth::id(), $studentId, null, null, [
            'leave_id'   => $id,
            'leave_date' => $this->request->input('leave_date')
        ]);

        Session::setFlash('success', 'Your leave application has been submitted for review.');
        $this->redirect('/student/leaves');
    }
}
