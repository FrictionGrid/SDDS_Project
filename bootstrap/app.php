<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom Authenticate middleware
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Register middleware aliases for permission and role checking
        $middleware->alias([
            'auth' => Authenticate::class,
            'permission' => CheckPermission::class,
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();

// Configure authentication redirect
app()->bind(
    \Illuminate\Contracts\Auth\Authenticatable::class,
    \App\Models\User::class
);
