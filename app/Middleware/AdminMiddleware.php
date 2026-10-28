<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Auth;

class AdminMiddleware {
    public function handle(Request $request): void {
        if (!Auth::check() || !Auth::hasRole('admin')) {
            Response::abort(403, 'Unauthorized. Administrator access required.');
        }
    }
}
