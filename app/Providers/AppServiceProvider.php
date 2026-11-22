<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.navigation', function ($view) {
            $user = Auth::user();
            $unreadNotificationsCount = 0;

            if ($user && method_exists($user, 'notifications')) {
                $unreadNotificationsCount = $user->notifications()
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('unreadNotificationsCount', $unreadNotificationsCount);
        });
    }
}