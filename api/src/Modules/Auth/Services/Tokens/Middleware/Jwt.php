<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Auth\Services\Tokens\JwtManager;
use Symfony\Component\HttpFoundation\Response;
use Modules\Auth\Services\Tokens\Middleware\AccessToken;
use Modules\Auth\Services\Tokens\Middleware\RefreshToken;
use Modules\Auth\Services\Tokens\Exceptions\TokenException;

class Jwt
{   
    public function __construct(protected JwtManager $jwtManager) {}

    public function handle(Request $request, Closure $next, ...$expectType): Response
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return $this->unauthorized('Token Bearer not provided');
        }

        try {
            $claims = $this->jwtManager->decode($token);

            if (!empty($expectType) && !in_array($claims->type_client ?? '', $expectType)) {
                return $this->unauthorized('Invalid token type for this route');
            }

            $this->resolveTokenStrategy($request, $claims);

            return $next($request);

        } catch (TokenException $e) {
            return $this->unauthorized($e->getMessage(), $e->getCode());
        }
    }

    protected function resolveTokenStrategy(Request $request, $claims): void
    { 
        match ($claims->type_client ?? null) {
            'internal' => AccessToken::execute($request, $claims),
            'refresh'  => RefreshToken::execute($request, $claims),
            default    => throw new TokenException('Unknown client type')
        };
    }

    protected function unauthorized(string $message, int $code = 401): Response
    {
        return response()->json(['message' => $message], $code);
    }
}