<?php

namespace App\Core;

use PDO;

abstract class Model {
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    public static function getPdo(): PDO {
        return Database::getInstance();
    }

    public static function all(string $orderBy = 'id DESC'): array {
        $pdo = self::getPdo();
        $stmt = $pdo->query("SELECT * FROM `" . static::$table . "` ORDER BY {$orderBy}");
        return $stmt->fetchAll();
    }

    public static function find(int|string $id): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findWhere(string $column, mixed $value): ?array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM `" . static::$table . "` WHERE `{$column}` = ? LIMIT 1");
        $stmt->execute([$value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id DESC'): array {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM `" . static::$table . "` WHERE `{$column}` = ? ORDER BY {$orderBy}");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int {
        $pdo = self::getPdo();
        $columns = array_keys($data);
        $fields = implode('`, `', $columns);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $sql = "INSERT INTO `" . static::$table . "` (`{$fields}`) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return (int) $pdo->lastInsertId();
    }

    public static function update(int|string $id, array $data): bool {
        $pdo = self::getPdo();
        $sets = [];
        $values = [];

        foreach ($data as $column => $value) {
            $sets[] = "`{$column}` = ?";
            $values[] = $value;
        }

        $values[] = $id;
        $sql = "UPDATE `" . static::$table . "` SET " . implode(', ', $sets) . " WHERE `" . static::$primaryKey . "` = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public static function delete(int|string $id): bool {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?");
        return $stmt->execute([$id]);
    }

    public static function count(string $where = '', array $params = []): int {
        $pdo = self::getPdo();
        $sql = "SELECT COUNT(*) FROM `" . static::$table . "`";
        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
