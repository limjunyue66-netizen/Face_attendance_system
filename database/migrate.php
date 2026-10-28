<?php
/**
 * Database Migration & Seeder CLI Script
 */
require_once __DIR__ . '/../config/database.php';

echo "=== Face Attendance System Database Installer ===\n";

try {
    $dbConfig = require __DIR__ . '/../config/database.php';
    $host = $dbConfig['host'];
    $port = $dbConfig['port'];
    $user = $dbConfig['username'];
    $pass = $dbConfig['password'];
    $database = $dbConfig['database'];

    echo "Connecting to MySQL server at {$host}:{$port}...\n";
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    echo "Reading install.sql...\n";
    $sqlFile = __DIR__ . '/install.sql';
    if (!file_exists($sqlFile)) {
        throw new RuntimeException("install.sql not found at {$sqlFile}");
    }

    $sql = file_get_contents($sqlFile);

    // Split SQL statements carefully
    echo "Executing database migration statements...\n";
    $pdo->exec($sql);

    echo "Migration completed successfully!\n";
    echo "Database: {$database}\n";
    echo "Tables and demo data are ready.\n";
    exit(0);
} catch (Exception $e) {
    echo "ERROR during migration: " . $e->getMessage() . "\n";
    exit(1);
}
