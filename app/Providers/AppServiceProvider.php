<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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
        // kích hoạt Bootstrap 5 cho phân trang
        // Paginator::useBootstrapFive();
        // kích hoạt Bootstrap 4 cho phân trang
        Paginator::useBootstrapFour();
    }
}
