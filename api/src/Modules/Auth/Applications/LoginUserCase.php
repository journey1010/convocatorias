<?php

namespace Modules\Auth\Applications;

use Modules\Auth\Services\Tokens\JwtManager;
use Modules\User\Services\UserAuth;
use Modules\Auth\Applications\Dtos\LoginUserDto;

class LoginUserCase {
    
    public function __construct(
        private JwtManager $jwt,
        private UserAuth $user
    ) {}

    public function exec(string $nickname, string $password): LoginUserDto
    {
        $user = $this->user->verifyCredentials($nickname, $password);

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

        $refreshToken = $this->jwt->generateRefreshToken($user->id, $user->token_version);

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