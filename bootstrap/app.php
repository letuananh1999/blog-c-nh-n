<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // API dành cho user
            Route::middleware(['api', 'auth:user'])
                ->prefix('api')
                ->name('api.')
                ->group(base_path('routes/api.php'));
            // API dành cho admin
            Route::middleware(['api', 'auth:admin'])
                ->prefix('api/admin')
                ->name('api.admin.')
                ->group(base_path('routes/admin.api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'checkrole' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
