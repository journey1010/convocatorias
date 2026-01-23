<?php

namespace Modules\Auth\Services\Tokens;

use stdClass;
use Modules\Auth\Services\Tokens\Enum\TokenType;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Modules\Auth\Services\Tokens\Contracts\TokenManager;
use Modules\Auth\Services\Tokens\JWTKeyManager;

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

    private function generateToken(string $subject, string $typeClient, ?int $ttl, ?int $version = null, ?array $customClaims = null): string
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

    public static function validateToken(string $token): stdClass
    {   
        $alg = config('jwt.alg');
        JWTKeyManager::loadKeys($alg);
        $privateKey = JWTKeyManager::getPrivateKey();
        $decoded = JWT::decode($token, new Key($privateKey, $alg));
        return $decoded;
    }
}