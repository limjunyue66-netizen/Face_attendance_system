<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class GuestMiddleware {
    public function handle(Request $request): void {
        if (Auth::check()) {
            $role = Auth::role();
            if ($role === 'admin') {
                Response::redirect('/admin/dashboard');
            } elseif ($role === 'teacher') {
                Response::redirect('/teacher/dashboard');
            } else {
                Response::redirect('/student/dashboard');
            }
        }
    }
}
