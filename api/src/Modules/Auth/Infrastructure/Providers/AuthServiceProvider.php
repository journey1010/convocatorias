<?php

namespace Modules\Auth\Infrastructure\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Auth\Services\Tokens\Middleware\Jwt;
use Modules\Auth\Infrastructure\Commands\JwtSecret;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void 
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/jwt.php',
            'jwt'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                JwtSecret::class,
            ]);
        }
        $this->registerMiddleware();
    }

    public function registerMiddleware(): void 
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('jwt', Jwt::class);
    }
}