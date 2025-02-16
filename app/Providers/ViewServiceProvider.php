<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
    public function boot(): void
    {
        View::composer(['product'], function ($view) {
            $view->with('categories', Category::all());
        });

        View::composer(['admin.categories.index'], function ($view) {
            $view->with('categories', Category::withCount('products')->get());
        });
    }
}
