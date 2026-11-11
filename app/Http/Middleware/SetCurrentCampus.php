<?php

namespace App\Http\Middleware;

use App\Models\Campus;
use App\Support\CampusContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentCampus
{
    public function handle(Request $request, Closure $next): Response
    {
        $context = app(CampusContext::class);
        $context->set(
            Campus::query()->where('slug', config('campus.default_slug'))->firstOrFail()
        );
        $defaultCampus = $context->campus();

        $routeCampus = $request->route('campus');

        if ($routeCampus !== null) {
            $campus = $routeCampus instanceof Campus
                ? $routeCampus
                : Campus::query()->where('slug', $routeCampus)->firstOrFail();

            abort_unless($campus->is_active, 404);
            $context->set($campus);
            URL::defaults(['campus' => $campus->slug]);
            view()->share('currentCampus', $campus);
            $request->route()->forgetParameter('campus');
        } else {
            $userCampus = $request->user()?->campus;

            if ($userCampus !== null && $userCampus->is_active) {
                $context->set($userCampus);
            }

            URL::defaults(['campus' => $context->campus()?->slug]);
            view()->share('currentCampus', $context->campus() ?? $defaultCampus);
        }

        return $next($request);
    }
}
