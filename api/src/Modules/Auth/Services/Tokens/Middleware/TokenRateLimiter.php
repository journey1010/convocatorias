<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Closure;
use App\Models\User;
use App\Models\Tokens;
use App\Models\Applications;
use App\Enum\CachePreFix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Services\Tokens\Enum\TokenType;
use Illuminate\Support\Facades\RateLimiter;

class TokenRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $sub = $request->attributes->get('sub');
        $typeClient = $request->attributes->get('type_client');

        $hourKey = $this->makeKey($sub, $typeClient, 'hour');
        $dailyKey = $this->makeKey($sub, $typeClient, 'day');

        $hourLimit = 100;
        $dailyLimit = 1000;

        if (RateLimiter::tooManyAttempts($hourKey, $hourLimit)) {
            if ($typeClient === TokenType::EXTERNAL->value) {
                $this->removeToken($sub);
                return response()->json(['message' => 'Token revoked due to too many requests per hour'], 429);
            }
            if($typeClient === TokenType::INTERNAL->value){
                User::where('id', $sub)->update(['status' => 2]);
            }
            
            return response()->json(['message' => 'You’ve reached the maximum number of requests for hour.'], 429);
        }

        if (RateLimiter::tooManyAttempts($dailyKey, $dailyLimit)) {
            return response()->json(['message' => 'You’ve reached the maximum number of requests for today.'], 429);
        }

        RateLimiter::hit($hourKey, now()->addHour());
        RateLimiter::hit($dailyKey, now()->addDay());

        return $next($request);
    }

    private function makeKey(int|string $sub, string $typeClient, string $window): string
    {
        return "rate:{$sub}:{$window}:{$typeClient}";
    }

    public function removeToken(string|int $sub): void
    {
        $key = CachePreFix::TOKEN->value . $sub;
        Tokens::where('sub', $sub)->delete();
        Applications::where('sub', $sub)->update([
            'status' => 0,
            'observacion' => 'Token revoked due to too many requests' 
        ]);
        Cache::forget($key);
    }
}
