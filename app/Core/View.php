<?php

namespace App\Core;

use App\Helpers\Url;
use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Session;

class View {
    public static function render(string $viewPath, array $data = [], ?string $layout = 'main'): void {
        $viewFile = dirname(__DIR__, 2) . '/views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            die("View template not found: {$viewPath}");
        }

        // Extract variables for view
        extract($data);

        // Flash messages
        $flashSuccess = Session::getFlash('success');
        $flashError   = Session::getFlash('error');
        $flashWarning = Session::getFlash('warning');
        $flashInfo    = Session::getFlash('info');

        // Current authenticated user
        $currentUser = Auth::user();

        // Render content buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutFile = dirname(__DIR__, 2) . "/views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}
