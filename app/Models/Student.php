<?php

namespace App\Models;

use App\Core\Model;

class Student extends Model {
    protected static string $table = 'students';
    protected static string $primaryKey = 'id';

    public static function getWithClass(int $id): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT s.*, c.class_name, c.class_code, c.start_time, c.late_grace_minutes, c.rest_start_time, c.rest_end_time, u.username
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.id = ? LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getAllWithClass(?string $search = null, ?int $classId = null, ?string $status = null): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT s.*, c.class_name, c.class_code, u.username
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (s.full_name LIKE ? OR s.student_code LIKE ? OR s.email LIKE ?)";
            $term = "%{$search}%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($classId)) {
            $sql .= " AND s.class_id = ?";
            $params[] = $classId;
        }

        if (!empty($status)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getRegisteredStudentsWithDescriptors(): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT s.id, s.student_code, s.full_name, s.class_id, s.preview_photo,
                   c.class_name, c.start_time, c.late_grace_minutes, c.rest_start_time, c.rest_end_time,
                   fd.descriptor, fd.registration_id
            FROM students s
            INNER JOIN classes c ON s.class_id = c.id
            INNER JOIN face_registrations fr ON fr.student_id = s.id AND fr.is_active = 1
            INNER JOIN face_descriptors fd ON fd.registration_id = fr.id
            WHERE s.status = 'active' AND s.face_registered = 1
            ORDER BY s.id ASC, fd.id ASC
        ";
        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll();

        // Group descriptors by student
        $students = [];
        foreach ($rows as $row) {
            $sid = (int) $row['id'];
            if (!isset($students[$sid])) {
                $students[$sid] = [
                    'id'                 => $sid,
                    'student_code'       => $row['student_code'],
                    'full_name'          => $row['full_name'],
                    'class_id'           => $row['class_id'],
                    'class_name'         => $row['class_name'],
                    'preview_photo'      => $row['preview_photo'],
                    'start_time'         => $row['start_time'],
                    'late_grace_minutes' => (int) $row['late_grace_minutes'],
                    'rest_start_time'    => $row['rest_start_time'],
                    'rest_end_time'      => $row['rest_end_time'],
                    'descriptors'        => []
                ];
            }
            $decoded = json_decode($row['descriptor'], true);
            if (is_array($decoded)) {
                $students[$sid]['descriptors'][] = $decoded;
            }
        }

        return array_values($students);
    }
}
