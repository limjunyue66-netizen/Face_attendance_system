<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;
use App\Helpers\Session;

class AuthMiddleware {
    public function handle(Request $request): void {
        if (!Auth::check()) {
            Session::setFlash('error', 'Please log in to access this page.');
            Response::redirect('/login');
        }
    }
}
