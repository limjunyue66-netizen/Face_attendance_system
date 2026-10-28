<?php

namespace App\Services;

class ExportService {
    public static function exportAttendanceCsv(array $records, string $format = 'csv'): void {
        $filename = "attendance_export_" . date('Ymd_His');

        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"{$filename}.xls\"");
            
            echo "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"></head><body>";
            echo "<table border='1'>";
            echo "<thead><tr style='background-color:#4f46e5;color:#ffffff;'>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Student Code</th>
                    <th>Class</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Confidence</th>
                    <th>Status</th>
                  </tr></thead><tbody>";

            foreach ($records as $r) {
                $confidence = !empty($r['check_in_confidence']) ? round($r['check_in_confidence'] * 100, 1) . '%' : 'N/A';
                echo "<tr>
                        <td>" . htmlspecialchars($r['attendance_date']) . "</td>
                        <td>" . htmlspecialchars($r['student_name']) . "</td>
                        <td>" . htmlspecialchars($r['student_code']) . "</td>
                        <td>" . htmlspecialchars($r['class_name'] ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($r['check_in_time'] ?? '-') . "</td>
                        <td>" . htmlspecialchars($r['check_out_time'] ?? '-') . "</td>
                        <td>" . $confidence . "</td>
                        <td>" . htmlspecialchars(ucfirst(str_replace('_', ' ', $r['status']))) . "</td>
                      </tr>";
            }
            echo "</tbody></table></body></html>";
            exit;
        }

        // Standard CSV with UTF-8 BOM
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Date',
            'Student Name',
            'Student Code',
            'Class',
            'Check In',
            'Check Out',
            'Confidence',
            'Status'
        ]);

        foreach ($records as $r) {
            $confidence = !empty($r['check_in_confidence']) ? round($r['check_in_confidence'] * 100, 1) . '%' : 'N/A';
            fputcsv($output, [
                $r['attendance_date'],
                $r['student_name'],
                $r['student_code'],
                $r['class_name'] ?? 'N/A',
                $r['check_in_time'] ?? '-',
                $r['check_out_time'] ?? '-',
                $confidence,
                ucfirst(str_replace('_', ' ', $r['status']))
            ]);
        }

        fclose($output);
        exit;
    }

    public static function exportStudentsCsv(array $students): void {
        $filename = "students_export_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Student ID',
            'Student Code',
            'Full Name',
            'Email',
            'Phone',
            'Gender',
            'Date of Birth',
            'Class',
            'Status',
            'Face Registered',
            'Registration Version'
        ]);

        foreach ($students as $s) {
            fputcsv($output, [
                $s['id'],
                $s['student_code'],
                $s['full_name'],
                $s['email'],
                $s['phone'] ?? '-',
                ucfirst($s['gender'] ?? 'other'),
                $s['date_of_birth'] ?? '-',
                $s['class_name'] ?? 'N/A',
                ucfirst($s['status']),
                $s['face_registered'] ? 'Yes' : 'No',
                $s['registration_version']
            ]);
        }

        fclose($output);
        exit;
    }
}
