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
        
        $claims = $user->toClaims();
        
        $accessToken = $this->jwt->generateAccessToken(
            $user->id,
            $claims->toArray()
        );
        
        $refreshToken = $this->jwt->generateRefreshToken($user->id);

        return new LoginUserDto(
            $user->name,
            $user->last_name,
            $user->dni,
            $user->email,
            $user->nickname,
            $user->getPermissionNames(),
            $user->getOfficeNames(),
            $accessToken,
            $refreshToken,
        );
    }
}