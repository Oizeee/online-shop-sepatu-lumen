<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class AuthMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = str_replace('Bearer ', '', $token);

        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        // simpan user ke request
        $request->auth = $user;

        return $next($request);
    }
}