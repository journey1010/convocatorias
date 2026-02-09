<?php

namespace Modules\Auth\Infrastructure\Context;

/**
 * Objeto inmutable que contiene los datos del usuario autenticado.
 * Esta es la abstracción principal para acceder a claims del JWT.
 * 
 */
final class RequestContext
{
    public function __construct(
        public readonly int $userId,
        public readonly int $level,
        public readonly string $dni,
        public readonly array $permissions,
        public readonly array $officeIds,
        public readonly array $localeIds,
    ) {}

    public function hasPermission(int $permissionId): bool
    {
        return in_array($permissionId, $this->permissions);
    }

    public function hasAnyPermission(array $permissionIds): bool
    {
        return !empty(array_intersect($permissionIds, $this->permissions));
    }

    public function hasOffice(int $officeId): bool
    {
        return in_array($officeId, $this->officeIds);
    }

    public function isEmployee(): bool
    {
        return !empty($this->officeIds);
    }
}
