<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah token dan user ada di session
        if (!session('api_token') || !session('user')) {
            // Jika request AJAX, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return redirect()->route('auth.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        return $next($request);
    }
}