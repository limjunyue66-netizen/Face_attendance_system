<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\AuditLog;

class AuditController extends Controller {
    public function index(): void {
        $action = $this->request->query('action');
        $startDate = $this->request->query('start_date');
        $endDate = $this->request->query('end_date');

        $logs = AuditLog::getFilteredLogs($action, $startDate, $endDate, 200);

        $this->view('admin.audit.index', [
            'title'     => 'System Audit Logs',
            'logs'      => $logs,
            'action'    => $action,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);
    }
}
