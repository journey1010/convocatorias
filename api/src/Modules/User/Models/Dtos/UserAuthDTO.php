<?php
namespace Modules\User\Models\Dtos;

use Modules\Auth\Services\Tokens\Claims\UserClaims;

class UserAuthDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $last_name,
        public readonly string $dni,
        public readonly string $nickname,
        public readonly string $phone,
        public readonly string $email,
        public readonly int $level,
        public readonly array $permissions = [],
        public readonly array $offices = []
    ) {}

    public function toClaims(): UserClaims
    {
        $claims = new UserClaims(
            userId: $this->id,
            dni: $this->dni,
            level: $this->level
        );

        // Agregar permisos si existen
        if (!empty($this->permissions['ids'])) {
            $claims->addClaim('permissions', implode(',', $this->permissions['ids']));
        }

        // Agregar oficinas solo si existen
        if (!empty($this->offices['ids'])) {
            $claims->addClaim('office_ids', implode(',', $this->offices['ids']));
            $claims->addClaim('locale_ids', implode(',', $this->offices['locale_ids']));
        }

        return $claims;
    }

    public function getOfficeNames(): string
    {
        return !empty($this->offices['names']) 
            ? implode(',', $this->offices['names']) 
            : '';
    }

    public function getPermissionNames(): array
    {
        return $this->permissions['names'] ?? [];
    }
}