<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Csrf;

class CsrfMiddleware {
    public function handle(Request $request): void {
        if ($request->isPost()) {
            $token = $request->input('_csrf_token') ?? $request->header('X-CSRF-TOKEN');

            // Allow bypass for internal kiosk attendance scanning API if explicitly marked
            $uri = $request->uri();
            if (str_starts_with($uri, '/api/')) {
                return;
            }

            if (!Csrf::validate($token)) {
                if ($request->isAjax()) {
                    Response::json(['success' => false, 'message' => 'CSRF verification token expired or invalid.'], 403);
                }
                Response::abort(403, 'CSRF verification token expired or invalid. Please refresh the page and try again.');
            }
        }
    }
}
