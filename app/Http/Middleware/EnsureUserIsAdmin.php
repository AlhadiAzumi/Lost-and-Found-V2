<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            in_array($request->user()?->role, ['admin', 'satpam'], true),
            403,
            'Akses ini hanya untuk admin atau petugas satpam.'
        );

        return $next($request);
    }
}
