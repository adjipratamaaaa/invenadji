<?php

use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckGudang;
use App\Http\Middleware\CheckKasir;
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
        // Register alias middleware
        $middleware->alias([
            'admin' => CheckAdmin::class,
            'gudang' => CheckGudang::class,
            'kasir' => CheckKasir::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();