<?php

/**
 * Facial Recognition Attendance Management System
 * Front Controller (public/index.php)
 */

declare(strict_types=1);

// Set default timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// Autoloader for App\* namespace
spl_autoload_register(function (string $class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Start session
\App\Helpers\Session::start();

// Initialize Request & Router
$request = new \App\Core\Request();
$router = new \App\Core\Router();

// Load Routes
$routesDefinition = require dirname(__DIR__) . '/config/routes.php';
$routesDefinition($router);

// Dispatch
$router->dispatch($request);
