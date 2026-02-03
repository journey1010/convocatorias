<?php 

namespace Modules\Accounts\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Services\Tokens\JwtManager;
use Modules\Auth\Services\Tokens\Enum\TokenType;
use Illuminate\Support\Facades\Cache;

class TokenController {
    
    public function generate(JwtManager $jwt, Request $request): string
    {
        $fingerprint = $request->header('X-Fingerprint');
        $ttl = 120; // 2 minutos

        $token = $jwt->generateToken(
            $fingerprint,
            TokenType::FOR_REGISTER->value,
            $ttl
        );

        Cache::put("register_token:{$fingerprint}", $token, $ttl);

        return $token;
    }
}