<?php
namespace Modules\Auth\Services\Tokens\Claims;

class UserClaims
{
    private array $claims = [];

    public function __construct(
        public readonly int $userId,
        public readonly string $dni,
        public readonly int $level
    ) {
        $this->claims = [
            'sub' => $userId,
            'dni' => $dni,
            'level' => $level
        ];
    }

    public function addClaim(string $key, mixed $value): self
    {
        $this->claims[$key] = $value;
        return $this;
    }

    public function addClaimIfNotEmpty(string $key, mixed $value): self
    {
        if (!empty($value)) {
            $this->claims[$key] = $value;
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->claims;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->claims[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->claims[$key]);
    }
}