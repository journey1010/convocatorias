<?php

namespace Modules\Auth\Applications\Dtos;

class LoginUserDto
{
    public function __construct(
        public string $name,
        public string $last_name,
        public string $dni,
        public string $email,
        public string $nickname,
        public array $permissions,
        public array $office,
        public string $tokenAccess,
        public string $tokenRefresh
    ) {}
}
