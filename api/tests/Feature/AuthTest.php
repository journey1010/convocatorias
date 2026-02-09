<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\UserHelper;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Modules\User\Enums\TypeUser;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_login_with_correct_credentials()
    {
        // Usamos un ciudadano (sin oficina)
        $userData = UserHelper::create([
            'type_user' => TypeUser::citizen->value
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => $userData['password'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'last_name',
                'dni',
                'email',
                'nickname',
                'permissions',
                'office', // Según tu DTO, esto vendría como null o string
                'tokenAccess',
                'tokenRefresh',
            ]);
        
        $userData = UserHelper::create([
            'type_user' => TypeUser::employee->value
        ]);

        $response = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => $userData['password'],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'last_name',
                'dni',
                'email',
                'nickname',
                'permissions',
                'office', // Según tu DTO, esto vendría como null o string
                'tokenAccess',
                'tokenRefresh',
            ]);
        


    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $userData = UserHelper::create();
        
        $response = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
    }

    public function test_employee_can_refresh_token_and_sees_office()
    {
        // Usamos un empleado (con oficina automática por factory)
        $userData = UserHelper::create([
            'type_user' => TypeUser::employee->value
        ]);
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => $userData['password'],
        ]);

        $refreshToken = $loginResponse->json('tokenRefresh');

        $response = $this->withHeader('Authorization', 'Bearer ' . $refreshToken)
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'nickname',
                'office', // Verificamos que trae oficina al ser empleado
                'tokenAccess',
                'tokenRefresh',
            ]);
            
        // Verificación extra de integridad
        $this->assertNotNull($response->json('office'));
    }
}