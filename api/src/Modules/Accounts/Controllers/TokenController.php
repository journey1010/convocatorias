<?php 

namespace Modules\Accounts\Controllers;

use Illuminate\Http\Request;
use Modules\Auth\Services\Tokens\JwtManager;
use Modules\Auth\Services\Tokens\Enum\TokenType;

class TokenController {
    
    public function generate(JwtManager $jwt, Request $request): string
    {
        return $jwt->generateToken(
            $request->header('X-Fingerprint'),
            TokenType::FOR_REGISTER->value,
            120
        );
    }
}