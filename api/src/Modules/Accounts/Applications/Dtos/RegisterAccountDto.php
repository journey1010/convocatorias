<?php 

namespace Modules\Accounts\Applications\Dtos;

class RegisterAccountDto {
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}
}