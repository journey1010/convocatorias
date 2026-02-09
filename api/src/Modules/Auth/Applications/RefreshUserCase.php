<?php

namespace Modules\Auth\Applications;

use Infrastructure\Exceptions\JsonResponseException;
use Modules\Auth\Applications\Dtos\LoginUserDto;
use Modules\Auth\Services\Tokens\JwtManager;
use Modules\User\Services\UserAuth;

class RefreshUserCase
{
    private const REFRESH_THRESHOLD = 8000000;

    public function __construct(
        private JwtManager $jwt,
        private UserAuth $user
    ) {}

    public function exec(int $user_id, ?int $exp, ?string $token): LoginUserDto
    {
        $user = $this->user->refreshUser($user_id);

        $claims = $user->toClaims();

        $accessToken = $this->jwt->generateAccessToken(
            $user->id,
            $claims->toArray()
        );

        $refreshToken = ($exp !== null && ($exp - time()) < self::REFRESH_THRESHOLD)
            ? $this->jwt->generateRefreshToken($user->id)
            : $token ?? throw new JsonResponseException('Unauthorized, token not provided', 401);

        return new LoginUserDto(
            $user->name,
            $user->last_name,
            $user->dni,
            $user->email,
            $user->nickname,
            $user->getPermissionNames(),
            $user->offices['names'] ?? [],
            $accessToken,
            $refreshToken,
        );
    }
}
