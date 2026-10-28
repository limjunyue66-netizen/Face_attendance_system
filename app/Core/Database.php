<?php

namespace App\Core;

use PDO;
use Exception;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (Exception $e) {
                // If in debug mode show error, else generic message
                die("Database connection error: " . htmlspecialchars($e->getMessage()));
            }
        }

        return self::$instance;
    }
}
