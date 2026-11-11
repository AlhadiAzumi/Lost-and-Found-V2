<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredEmail = config('campus.platform_admin_email');
        $user = $request->user();

        abort_unless(
            filled($configuredEmail)
            && $user !== null
            && $user->role === 'admin'
            && hash_equals(mb_strtolower($configuredEmail), mb_strtolower($user->email)),
            403,
            'Akses ini hanya untuk admin platform yang dikonfigurasi.'
        );

        return $next($request);
    }
}
