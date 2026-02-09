<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Infrastructure\Context\RequestContext;

class PermissionChecker
{
    public function hasPermissionByName(RequestContext $context, string $permissionName): bool
    {
        if (in_array('*', $this->getPermissionNames($context->permissions))) {
            return true;
        }

        $permissionNames = $this->getPermissionNames($context->permissions);
        return in_array($permissionName, $permissionNames);
    }

    public function hasAnyPermissionByName(RequestContext $context, array $permissionNames): bool
    {
        $userPermissions = $this->getPermissionNames($context->permissions);

        if (in_array('*', $userPermissions)) {
            return true;
        }

        return !empty(array_intersect($permissionNames, $userPermissions));
    }
   
    public function hasAllPermissionsByName(RequestContext $context, array $permissionNames): bool
    {
        $userPermissions = $this->getPermissionNames($context->permissions);

        if (in_array('*', $userPermissions)) {
            return true;
        }

        return empty(array_diff($permissionNames, $userPermissions));
    }

    private function getPermissionNames(array $permissionIds): array
    {
        if (empty($permissionIds)) {
            return [];
        }

        sort($permissionIds);
        $cacheKey = 'permissions_names_' . implode('_', $permissionIds);

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($permissionIds) {
            return DB::table('permissions')
                ->whereIn('id', $permissionIds)
                ->orderBy('id', 'asc')
                ->pluck('name')
                ->toArray();
        });
    }
}
