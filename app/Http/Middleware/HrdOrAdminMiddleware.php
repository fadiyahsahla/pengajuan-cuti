<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HrdOrAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || (!$user->isAdmin() && !$user->isHRD())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. HRD or Admin access required.'
                ], 403);
            }
            abort(403, 'Unauthorized. HRD or Admin access required.');
        }

        return $next($request);
    }
}
