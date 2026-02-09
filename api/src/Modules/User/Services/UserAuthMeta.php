<?php

namespace Modules\User\Services;

use Modules\Rbac\Services\AccessControlServices;

class UserAuthMeta
{

    use AccessControlServices;

    public function __construct(
        readonly public int $user_id,
        readonly public array $permissions,
        readonly public ?array $offices
    ) {}

    public function hasPermission(string $permission)
    {
        $permissionsString = !empty($this->permissions)
            ? implode(',', $this->permissions)
            : '';
        return $this->verifyPermission($permissionsString, $permission);
    }
}
