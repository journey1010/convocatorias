<?php

namespace Tests\Helpers;

use Modules\Office\Models\Office;
use Modules\User\Models\User;
use Modules\User\Enums\TypeUser;
use Modules\User\Enums\StatusUser;
use Modules\User\Models\OfficeUser;

class UserHelper
{
    /**
     * Crea un usuario con roles y permisos específicos
     * 
     * @param array $userData Datos del usuario (name, last_name, email, etc)
     * @param array $roles Roles a asignar (ej: ['p.postulante'])
     * @param array $permissions Permisos directos (ej: ['create_user'])
     * @return array ['user' => User, 'nickname' => string, 'password' => string]
     */
    public static function create(
        array $userData = [],
        array $roles = [],
        array $permissions = []
    ): array {
        $password = 'test_password_123';
        $nickname = 'test_user_' . uniqid();
        
        $defaultData = [
            'name' => 'Test',
            'last_name' => 'User',
            'dni' => (string) rand(10000000, 99999999),
            'nickname' => $nickname,
            'email' => 'test_' . uniqid() . '@example.com',
            'phone' => '987654321',
            'password' => bcrypt($password),
            'status' => StatusUser::ACTIVE->value,
            'type_user' => TypeUser::citizen->value,
            'level' => 1,
        ];

        $finalData = array_merge($defaultData, $userData);
        
        // Crear usuario
        $user = User::create($finalData);
        
        // Asignar roles
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }
        
        // Asignar permisos directos
        if (!empty($permissions)) {
            $user->syncPermissions($permissions);
        }

        if($user->type_user !== TypeUser::citizen->value) {
            $office = Office::factory()->create([
                'user_id' => $user->id,
                'name' => 'Oficina de ' . $user->name,
            ]);

            OfficeUser::create([
                'user_id' => $user->id,
                'office_id' => $office->id,
            ]);
        }

        return [
            'user' => $user,
            'nickname' => $finalData['nickname'],
            'password' => $password,
        ];
    }

    /**
     * Crea un usuario postulante (sin oficina)
     * 
     * @param array $userData Datos adicionales del usuario
     * @return array ['user' => User, 'nickname' => string, 'password' => string]
     */
    public static function createApplicant(array $userData = []): array
    {
        return self::create(
            array_merge([
                'type_user' => TypeUser::citizen->value,
            ], $userData),
            roles: ['p.postulante']
        );
    }
}