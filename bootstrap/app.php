<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ EXCLUDE API TOKEN FROM ENCRYPTION
        $middleware->encryptCookies(except: [
            'api_token',
        ]);

        // ✅ GLOBAL CORS
        $middleware->append(HandleCors::class);

        // ✅ Sanctum cookie-based auth
        $middleware->appendToGroup('api', [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // ✅ REGISTER ADMIN + TOKEN AUTH MIDDLEWARE
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'token.auth' => \App\Http\Middleware\TokenAuthMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
