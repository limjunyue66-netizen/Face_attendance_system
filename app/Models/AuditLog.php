<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model {
    protected static string $table = 'audit_logs';
    protected static string $primaryKey = 'id';

    public static function log(
        string $action,
        ?int $userId = null,
        ?int $studentId = null,
        ?float $confidence = null,
        ?int $version = null,
        ?array $metadata = null
    ): void {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 250);

            self::create([
                'user_id'              => $userId,
                'student_id'           => $studentId,
                'action'               => $action,
                'ip_address'           => $ip,
                'user_agent'           => $ua,
                'confidence_score'     => $confidence,
                'registration_version' => $version,
                'metadata'             => $metadata ? json_encode($metadata) : null,
            ]);
        } catch (\Throwable $e) {
            // Silently ignore log insertion failures to prevent breaking user workflow
            error_log("AuditLog error: " . $e->getMessage());
        }
    }

    public static function getFilteredLogs(
        ?string $action = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $limit = 200
    ): array {
        $pdo = self::getPdo();
        $sql = "
            SELECT al.*, u.username, u.role as user_role, s.full_name as student_name, s.student_code
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN students s ON al.student_id = s.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($action)) {
            $sql .= " AND al.action LIKE ?";
            $params[] = "%{$action}%";
        }

        if (!empty($startDate)) {
            $sql .= " AND DATE(al.created_at) >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND DATE(al.created_at) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY al.id DESC LIMIT " . (int)$limit;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
