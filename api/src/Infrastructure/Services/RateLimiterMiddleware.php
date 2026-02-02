<?php

namespace Infrastructure\Services;

use Closure;
use Illuminate\Http\Request;
use Infrastructure\Services\FingerPrint;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterMiddleware {
    
    public function handle(Request $request, \Closure $next, string $name = 'api', int $maxAttempts = 5, int $decayMinutes = 1)
    {    
        $fingerprint = FingerPrint::generate($request);
        $key = "throttle:{$name}:{$fingerprint}";

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Demasiadas solicitudes. Por seguridad, su acceso ha sido pausado temporalmente.',
                'retry_after' => RateLimiter::availableIn($key) . ' segundos',
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $request->headers->set('X-Fingerprint', $fingerprint);
        
        return $next($request);
    }
}