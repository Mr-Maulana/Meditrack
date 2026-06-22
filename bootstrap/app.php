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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastSeen::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'apoteker' => \App\Http\Middleware\IsApoteker::class,
            'kurir' => \App\Http\Middleware\IsKurir::class,
            'admin_apoteker' => \App\Http\Middleware\IsAdminOrApoteker::class,
            'operator' => \App\Http\Middleware\IsOperator::class,
            'dokter' => \App\Http\Middleware\IsDokter::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
