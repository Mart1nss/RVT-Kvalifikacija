<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
                $view->with('unreadNotifications', Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->latest()
                    ->get());
            }
        });
    }
}
