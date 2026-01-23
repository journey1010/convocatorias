<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function(Request $request){
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip())->response(function(Request $request){
                return response()->json(['message' => 'To many request'], 429);
            });
        });

        RateLimiter::for('login', function(Request $request){
            return Limit::perMinute(5)->by($request->user()?->id ?? $request->ip())->response(function (Request $request) {
                return response()->json(['message' => 'Too many requests'], 429);
            });
        });

        RateLimiter::for('web', function(Request $request){
            return Limit::perMinute(1000)->by($request->user()?->id ?? $request->ip())->response(function (Request $request) {
                return response()->json(['message' => 'Too many requests'], 429);
            });
        });
    }
}
