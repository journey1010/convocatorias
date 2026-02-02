<?php

namespace Modules\Auth\Services\Tokens;

use stdClass;
use Exception;
use UnexpectedValueException;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Services\Tokens\Enum\TokenType;
use Firebase\JWT\{
    Key, 
    JWT, 
    ExpiredException
};
use Modules\Auth\Services\Tokens\{
    Contracts\TokenManager,
    JWTKeyManager
};
use Firebase\JWT\{
    BeforeValidException,
    SignatureInvalidException
};
use Modules\Auth\Services\Tokens\Exceptions\TokenException;

class JwtManager implements TokenManager
{
    protected string $alg;
    protected string $privateKey;
    protected int $accessTtl; 
    protected int $refreshTtl;

    public function __construct()
    {
        $this->alg = config('jwt.alg');
        JWTKeyManager::loadKeys($this->alg);
        $this->privateKey = JWTKeyManager::getPrivateKey();
    }

    public function generateToken(string $subject, string $typeClient, ?int $ttl, ?int $version = null, ?array $customClaims = null): string
    {
        $issuedAt = time();

        $payload = [
            'sub' => $subject,
            'type_client' => $typeClient,
        ];
        
        if ($ttl !== null) {
            $payload['exp'] = $issuedAt + $ttl;
        }

        if($version){
            $payload['ver'] = $version;
        }

        if ($customClaims) {
            $payload = array_merge($payload, $customClaims);
        }

        return JWT::encode($payload, $this->privateKey, $this->alg);
    }

    public function generateAccessToken(int|string $subject, ?int $version = null, ?array $claims = null): string
    {
        $this->accessTtl  =  config('jwt.ttl_access'); 
        return $this->generateToken(
            subject: $subject,
            typeClient: TokenType::INTERNAL->value, 
            ttl: $this->accessTtl,
            customClaims: $claims
        );
    }

    public function generateRefreshToken(int|string $subject, ?int $version = null): string
    {
        $this->refreshTtl = config('jwt.ttl_refresh');
        return $this->generateToken(
            subject: $subject,
            typeClient: TokenType::REFRESH->value,
            ttl: $this->refreshTtl
        );
    }

    public function generateAppToken(int $applicationId, array $ip, string $role): string
    {
        return $this->generateToken(
            subject: $applicationId,
            typeClient: TokenType::EXTERNAL->value,
            ttl: null, 
            customClaims: ['ip' => $ip, 'role' => $role]
        );
    }

    public function decode(string $token): stdClass
    {   
        try {
            return JWT::decode($token, new Key($this->privateKey, $this->alg));
        } catch (ExpiredException $e) {
            throw new TokenException('Token has expired', 401, $e);
        } catch (SignatureInvalidException | BeforeValidException | UnexpectedValueException $e) {
            throw new TokenException('Invalid token signature or structure', 401, $e);
        } catch (\Exception $e) {
            Log::error('JWT error: ' . $e->getMessage(), [
                'exception'        => get_class($e),
                'file'             => $e->getFile(),
                'line'             => $e->getLine(),
                'code'             => $e->getCode(),
                'previous'         => $e->getPrevious() ? get_class($e->getPrevious()) : null,
                'trace_snippet'    => collect($e->getTrace())->take(5)->toArray(),
                'timestamp'        => now()->toIso8601String(),
            ]);
            throw new TokenException('Could not validate token', 500, $e);
        }
    }
}