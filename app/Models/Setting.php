<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model {
    protected static string $table = 'settings';
    protected static string $primaryKey = 'id';
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $row = self::findWhere('setting_key', $key);
        if ($row) {
            self::$cache[$key] = $row['setting_value'];
            return $row['setting_value'];
        }

        return $default;
    }

    public static function getAllKeyed(): array {
        $rows = self::all('id ASC');
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    public static function set(string $key, mixed $value, ?string $description = null): void {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, description)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$key, (string) $value, $description]);
        self::$cache[$key] = (string) $value;
    }
}
