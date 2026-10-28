<?php

namespace App\Core;

use App\Helpers\Session;

abstract class Controller {
    protected Request $request;

    public function __construct() {
        $this->request = new Request();
    }

    protected function view(string $viewPath, array $data = [], ?string $layout = 'main'): void {
        View::render($viewPath, $data, $layout);
    }

    protected function json(array $data, int $statusCode = 200): void {
        Response::json($data, $statusCode);
    }

    protected function redirect(string $path): void {
        Response::redirect($path);
    }

    protected function setFlash(string $type, string $message): void {
        Session::setFlash($type, $message);
    }
}
