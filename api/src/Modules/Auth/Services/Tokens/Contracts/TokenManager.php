<?php

namespace Modules\Auth\Services\Tokens\Contracts;

use stdClass;

interface TokenManager
{
    public function generateAccessToken(int|string $subject, ?array $claims = null): string;
    public function generateRefreshToken(int|string $subject) : string;
    public function decode(string $token):  stdClass;
}