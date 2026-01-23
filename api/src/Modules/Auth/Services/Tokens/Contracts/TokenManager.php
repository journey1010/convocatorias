<?php

namespace Modules\Auth\Services\Tokens\Contracts;

use stdClass;

interface TokenManager
{
    public function generateAccessToken(int|string $subject, ?int $version = null, ?array $claims = null): string;
    public function generateRefreshToken(int|string $subject, ?int $version = null): string;
    public static function validateToken(string $token):  stdClass;
}