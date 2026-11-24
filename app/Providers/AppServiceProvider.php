<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

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
        View::composer('layouts.app', function (ViewInstance $view): void {
            $user = auth()->user();
            $unreadNotificationCount = $user instanceof User
                ? $user->unreadNotifications()->count()
                : 0;

            $view->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
