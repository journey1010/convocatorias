<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Infrastructure\Services\GlobalResponseHandlerException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function(){
            Route::prefix('api')
                 ->middleware('throttle:api')
                 ->group(base_path('routes/api.php'));
            
            Route::middleware('web')
                 ->middleware('throttle:web')
                 ->group(base_path('routes/web.php'));
        },   
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            return GlobalResponseHandlerException::handler($e, $request);
        });
    })->create();