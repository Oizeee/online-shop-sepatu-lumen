<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = $request->auth;

        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Access denied. Admin only.'
            ], 403);
        }

        return $next($request);
    }
}