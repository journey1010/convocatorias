<?php

namespace Modules\Auth\Services\Tokens\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use UnexpectedValueException;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Services\Tokens\JwtManager;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\HttpFoundation\Response;
use Modules\Auth\Services\Tokens\Middleware\AccessToken;
use Modules\Auth\Services\Tokens\Middleware\RefreshToken;
use Modules\Auth\Services\Tokens\Exceptions\TokenException;

class Jwt
{   
    public function handle(Request $request, Closure $next, ...$expectType): Response
    {
        try {
            $token = $request->bearerToken();
            if (empty($token)) {
                throw new TokenException('Token Bearer not provided');
            }

            $claims = (new JwtManager)->validateToken($token);

            if (!in_array($claims->type_client, $expectType)) {
                throw new TokenException('Invalid token type for this route');
            }
            
            match ($claims->type_client ?? null) {
                'internal' => AccessToken::execute($request, $claims),
                'refresh' => RefreshToken::execute($request, $claims),
                default => throw new TokenException('Cliente desconocido')
            };

            return $next($request);
        } catch (ExpiredException $e) {
            return $this->unauthorized('Expired token');
        } catch (TokenException $e){
            return $this->unauthorized($e->getMessage());
        } catch (SignatureInvalidException | BeforeValidException | UnexpectedValueException  | Exception $e) {
            Log::error('JWT error: ' . $e->getMessage(), [
                'exception'        => get_class($e),
                'file'             => $e->getFile(),
                'line'             => $e->getLine(),
                'code'             => $e->getCode(),
                'previous'         => $e->getPrevious() ? get_class($e->getPrevious()) : null,
                'trace_snippet'    => collect($e->getTrace())->take(5)->toArray(),
                'timestamp'        => now()->toIso8601String(),
            ]);
            return $this->unauthorized('Invalid token');
        }
    }

    protected function unauthorized(string $message): Response
    {
        return response()->json(['message' => $message], 401);
    }
}