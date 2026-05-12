<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            at: '*',
            headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
                     \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT,
        );

        // Livewire upload-file is secured by signed URL — exclude from CSRF
        // (CSRF fails in Replit's iframe preview because session cookies are
        // not attached to the XHR due to SameSite restrictions)
        $middleware->validateCsrfTokens(except: [
            'livewire/upload-file',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
