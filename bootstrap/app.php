<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',  // Deshabilitado: los controladores Api/ no existen
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Headers de seguridad HTTP aplicados globalmente
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'web.auth'    => \App\Http\Middleware\WebAuth::class,
            'web.admin'   => \App\Http\Middleware\WebAdmin::class,
            'web.tecnico' => \App\Http\Middleware\WebTecnico::class,
            'role'        => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();