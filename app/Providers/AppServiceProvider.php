<?php

namespace App\Providers;

use App\Models\UserNotification;
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
        View::composer('layouts.app', function ($view) {
            $userNotifications = [];
            if (Auth::check()) {
                $user = Auth::user();
                $userNotifications = $user->userNotifications()
                    ->latest()
                    ->take(10)
                    ->get();
            }
            $view->with('notifications', $userNotifications);
        });
    }
}
