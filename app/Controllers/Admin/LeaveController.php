<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\LeaveApplication;
use App\Models\AuditLog;
use App\Helpers\Session;
use App\Helpers\Auth;

class LeaveController extends Controller {
    public function index(): void {
        $status = $this->request->query('status');
        $leaves = LeaveApplication::getAllDetailed($status);

        $this->view('admin.leaves.index', [
            'title'  => 'Student Leave Applications',
            'leaves' => $leaves,
            'status' => $status
        ]);
    }

    public function show(int $id): void {
        $leave = LeaveApplication::find($id);
        if (!$leave) {
            Session::setFlash('error', 'Leave application not found.');
            $this->redirect('/admin/leaves');
        }

        $detailed = LeaveApplication::getAllDetailed(null, $leave['student_id']);
        $item = null;
        foreach ($detailed as $d) {
            if ((int)$d['id'] === $id) {
                $item = $d;
                break;
            }
        }

        $this->view('admin.leaves.show', [
            'title' => "Review Leave Application #{$id}",
            'leave' => $item ?: $leave
        ]);
    }

    public function review(int $id): void {
        $leave = LeaveApplication::find($id);
        if (!$leave) {
            Session::setFlash('error', 'Leave application not found.');
            $this->redirect('/admin/leaves');
        }

        $action = $this->request->input('action'); // 'approved' or 'rejected'
        $note = trim((string)$this->request->input('review_note'));

        if (!in_array($action, ['approved', 'rejected'], true)) {
            Session::setFlash('error', 'Invalid action selected.');
            $this->redirect("/admin/leaves/{$id}");
        }

        LeaveApplication::update($id, [
            'status'              => $action,
            'review_note'         => $note,
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at'         => date('Y-m-d H:i:s')
        ]);

        $logAction = ($action === 'approved') ? 'LEAVE_APPROVED' : 'LEAVE_REJECTED';
        AuditLog::log($logAction, Auth::id(), $leave['student_id'], null, null, [
            'leave_id'    => $id,
            'leave_date'  => $leave['leave_date'],
            'review_note' => $note
        ]);

        Session::setFlash('success', "Leave application #{$id} has been {$action}.");
        $this->redirect('/admin/leaves');
    }
}
