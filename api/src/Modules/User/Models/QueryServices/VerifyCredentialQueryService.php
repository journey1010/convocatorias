<?php 

namespace Modules\User\Models\QueryServices;

use Illuminate\Support\Facades\DB;

class VerifyCredentialQueryService 
{
    /**
     *  @param int|string $userIdentifier
     *  @return ?object $user
     */
    protected function searchByNickname(int|string $userIdentifier): ?object
    {
        return DB::table('users')
            ->select([
                'id', 
                'name',
                'last_name',
                'dni',
                'nickname',
                'phone',
                'email',
                'status',
                'level',
                'password',
                'token_version'
            ])
            ->when(is_int($userIdentifier), fn($q) => $q->where('id', $userIdentifier))
            ->when(is_string($userIdentifier), fn($q) => $q->where('nickname', $userIdentifier))
            ->first();
    }

    /**
     * Obtiene todos los permisos únicos de un usuario
     * 
     * Combina dos fuentes de permisos:
     * 1. Permisos directos (permission_user)
     * 2. Permisos heredados de roles (role_user → permission_role)
     * 
     * @return array Array asociativo con:
     *               - 'ids': array de IDs de permisos [1, 5, 8, 12]
     *               - 'names': array de nombres ['create_user', 'edit_user', ...]
     */
    protected function getUserPermissions(int $userId): array
    {
        $directPermissions = DB::table('permission_user as pu')
            ->select('p.id', 'p.name')
            ->join('permissions as p', 'p.id', '=', 'pu.permission_id')
            ->where('pu.user_id', $userId);

        $permissions = DB::table('role_user as ru')
            ->select('p.id', 'p.name')
            ->join('permission_role as pr', 'pr.role_id', '=', 'ru.role_id')
            ->join('permissions as p', 'p.id', '=', 'pr.permission_id')
            ->where('ru.user_id', $userId)
            ->union($directPermissions) 
            ->orderBy('id')
            ->get();

        return [
            'ids' => $permissions->pluck('id')->toArray(),
            'names' => $permissions->pluck('name')->toArray(),
        ];
    }
}