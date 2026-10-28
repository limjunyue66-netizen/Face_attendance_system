<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class TeacherMiddleware {
    public function handle(Request $request): void {
        if (!Auth::check() || !Auth::hasRole('teacher')) {
            Response::abort(403, 'Unauthorized. Teacher access required.');
        }
    }
}
