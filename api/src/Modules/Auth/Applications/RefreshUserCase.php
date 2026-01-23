<?php 

namespace Modules\Auth\Applications;

use Infrastructure\Exceptions\JsonResponseException;
use Modules\Auth\Applications\Dtos\LoginUserDto;
use Modules\Auth\Services\Tokens\JwtManager;
use Modules\User\Services\UserAuth;

class RefreshUserCase {

    private const REFRESH_THRESHOLD = 8000000;

    public function __construct(
        private JwtManager $jwt,
        private UserAuth $user
    ) {}

    public function exec(int $user_id, ?int $exp, ?string $token): LoginUserDto
    {
        $user = $this->user->refreshUser($user_id);
        
        $accessToken = $this->jwt->generateAccessToken(
            $user->id,
            $user->token_version,
            [
                'dni' => $user->dni,
                'level' => $user->level,
                'permissions' => implode(',', $user->permissions['ids']),
                'office_ids' => $user->offices['ids']
            ]
        );

        $refreshToken = ($exp !== null && ($exp - time()) < self::REFRESH_THRESHOLD)
            ? $this->jwt->generateRefreshToken($user->id, $user->token_version)
            : $token ?? throw new JsonResponseException('Unauthorized, token not provided', 401);
        
        $offices = implode(',', $user->offices['names']);

        return new LoginUserDto(
            $user->name,
            $user->last_name,
            $user->dni,
            $user->email,
            $user->nickname,
            $user->permissions['names'],
            $offices,
            $accessToken,
            $refreshToken,
        );
    }
}