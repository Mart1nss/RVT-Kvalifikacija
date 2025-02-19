<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class NotificationsViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        View::composer('navbar', function ($view) {
            if (Auth::check()) {
                $view->with('unreadNotifications', Auth::user()->unreadNotifications);
            }
        });
    }
}
