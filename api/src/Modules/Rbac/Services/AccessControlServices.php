<?php

namespace Modules\Rbac\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait AccessControlServices {
    
    public array $permission_users = [];

    protected function verifyPermission(string $stringPermission, string|array $permission, bool $strict = false): bool
    {
        $this->permission = $this->generateCacheKey($stringPermission);
        if (in_array('*', $this->permission)) {
            return true;
        }
        $permission = is_array($permission) ? $permission : [$permission];
        if($strict){
            return empty(array_diff($permission, $this->permission));
        }
        return count(array_intersect($permission, $this->permission)) > 0;
    }

    protected function retriveRbac(string $stringPermission): array
    {
        return $this->generateCacheKey($stringPermission);
    }
    
    private function generateCacheKey(string $permission)
    {
        $permissionList = collect(explode(',', $permission))
            ->map(fn($r) => trim($r))
            ->sort()
            ->values()
            ->all();

        $permissionKey = implode('_', $permissionList); 

        $cacheKey = "crp_role_{$permissionKey}";
    
        return Cache::remember($cacheKey, now()->addDays(6), function () use ($permissionList) {
            $results = DB::table('permissions')
                ->whereIn('id', $permissionList)
                ->orderBy('id', 'asc')
                ->pluck('name') 
                ->toArray();
            return $results;
        });
    }
}