<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/console.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('login'));

        $middleware->alias([
            'secure.file.upload' => \App\Http\Middleware\SecureFileUpload::class,
            'ban' => \App\Http\Middleware\CheckBan::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'check.active' => \App\Http\Middleware\CheckActive::class,
            'block.devtools' => \App\Http\Middleware\BlockDevTools::class,
        ]);

        $middleware->web([
            \App\Http\Middleware\SecureFileUpload::class,
            \App\Http\Middleware\CheckActive::class,
            \App\Http\Middleware\BlockDevTools::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'card-deposit/callback',
            'bank-auto-deposit/callback'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
