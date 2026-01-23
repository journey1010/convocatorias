<?php

namespace Modules\User\Models\Dtos;

class UserFiltersDTO {
    public function __construct(
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?array $select = null,
        public readonly ?array $userFilters = null,      // ['status' => 1, 'email' => 'test@example.com']
        public readonly ?array $roleIds = null,          // [1, 2, 3]
        public readonly ?array $permissionIds = null,    // [5, 6]
        public readonly ?array $officeIds = null,       // [10, 11]
        public readonly ?array $context = null,
    ) {}
}