<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\RestAttendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\ExportService;

class AttendanceController extends Controller {
    public function index(): void {
        $date = $this->request->query('date');
        $classId = (int) $this->request->query('class_id') ?: null;
        $studentId = (int) $this->request->query('student_id') ?: null;
        $status = $this->request->query('status');

        $records = Attendance::getFilteredRecords($date, $classId, $studentId, $status);
        $classes = SchoolClass::all('class_name ASC');
        $students = Student::all('full_name ASC');

        $this->view('admin.attendance.index', [
            'title'     => 'Attendance Records',
            'records'   => $records,
            'classes'   => $classes,
            'students'  => $students,
            'date'      => $date,
            'classId'   => $classId,
            'studentId' => $studentId,
            'status'    => $status,
        ]);
    }

    public function rest(): void {
        $date = $this->request->query('date');
        $classId = (int) $this->request->query('class_id') ?: null;

        $records = RestAttendance::getFilteredRecords($date, $classId);
        $classes = SchoolClass::all('class_name ASC');

        $this->view('admin.attendance.rest', [
            'title'   => 'Rest / Break Attendance Records',
            'records' => $records,
            'classes' => $classes,
            'date'    => $date,
            'classId' => $classId
        ]);
    }

    public function export(): void {
        $format = $this->request->query('format', 'csv'); // 'csv' or 'excel'
        $date = $this->request->query('date');
        $classId = (int) $this->request->query('class_id') ?: null;
        $studentId = (int) $this->request->query('student_id') ?: null;
        $status = $this->request->query('status');

        $records = Attendance::getFilteredRecords($date, $classId, $studentId, $status);
        ExportService::exportAttendanceCsv($records, $format);
    }
}
