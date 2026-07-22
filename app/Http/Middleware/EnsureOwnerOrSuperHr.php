<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerOrSuperHr
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = session('user.role');
        $isSuperHr = session('user.is_super_hr');

        if ($role !== 'owner' && !($role === 'hr' && $isSuperHr)) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        return $next($request);
    }
}