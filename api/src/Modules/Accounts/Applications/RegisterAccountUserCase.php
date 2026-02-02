<?php

namespace Modules\Accounts\Applications;

use Exception;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Services\Tokens\JwtManager;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Infrastructure\Exceptions\JsonResponseException;
use Modules\Auth\Services\Tokens\Exceptions\TokenException;

class RegisterAccountUserCase {
   
    public function __construct(protected JwtManager $jwtManager) {}

    public function exec(Request $request): void
    {
        $inputs = $request->all();
    
    }

    private function checkToken(string $token): bool
    {
        try {
            $claims = $this->jwtManager->decode($token);
            $claims->type_client 
            
        } catch (TokenException $e) {
            $msg = $e->getMessage() === 'Token has expired' 
                ? 'Límite de tiempo de registro alcanzado' 
                : 'Seguridad fallida';
                
            throw new JsonResponseException($msg, $e->getCode());
        }
    }
}