<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class StudentMiddleware {
    public function handle(Request $request): void {
        if (!Auth::check() || !Auth::hasRole('student')) {
            Response::abort(403, 'Unauthorized. Student access required.');
        }
    }
}
