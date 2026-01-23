<?php

namespace Modules\Auth\Services\Tokens;

use Exception;

class JWTKeyManager
{
    private static string $privateKey;
    private static ?string $publicKey = null;

    public static function loadKeys(string $alg): void
    {
        if (in_array($alg, ['RS256', 'RS384', 'RS512', 'ES256', 'ES384', 'ES256K', 'EdDSA'])) {
            self::$privateKey = config('jwt.private_key') ?: throw new Exception("Missing private key for algorithm: $alg");
            if (in_array($alg, ['RS256', 'RS384', 'RS512', 'EdDSA'])) {
                self::$publicKey = config('jwt.public_key') ?: throw new Exception("Missing public key for algorithm: $alg");
            }
        } elseif (str_starts_with($alg, 'HS')) {
            self::$privateKey = config('jwt.secret') ?: throw new Exception("Missing secret key for algorithm: $alg");
        } else {
            throw new Exception("Unsupported algorithm: $alg");
        }
    }

    public static function getPrivateKey(): string
    {
        return self::$privateKey;
    }

    public static function getPublicKey(): ?string
    {
        return self::$publicKey;
    }

    public static function needsPublicKey(string $alg): bool
    {
        return in_array($alg, ['RS256', 'RS384', 'RS512', 'EdDSA']);
    }
}