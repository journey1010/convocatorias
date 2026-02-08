<?php

namespace Tests\Helpers;

use Modules\User\Models\User;
use Modules\User\Enums\TypeUser;

class UserHelper
{
    public static function create(
        array $userData = [],
        array $roles = [],
        array $permissions = []
    ): array {
        $password = 'test_password_123';
        
        $type = $userData['type_user'] ?? TypeUser::citizen->value;
        $factory = User::factory();

        if ($type === TypeUser::employee->value) {
            $factory = $factory->employee();
        } else {
            $factory = $factory->citizen();
        }

        // 2. Crear usuario
        $user = $factory->create(array_merge([
            'password' => bcrypt($password),
        ], $userData));
        
        // 3. Roles y Permisos
        if (!empty($roles)) $user->syncRoles($roles);
        if (!empty($permissions)) $user->syncPermissions($permissions);

        return [
            'user' => $user,
            'nickname' => $user->nickname,
            'password' => $password,
        ];
    }

    public static function createApplicant(array $userData = []): array
    {
        return self::create(
            array_merge(['type_user' => TypeUser::citizen->value], $userData),
            ['p.postulante']
        );
    }
}