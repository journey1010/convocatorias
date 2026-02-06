<?php

namespace Tests\Helpers;

class AuthenticationHelper
{
    /**
     * Autentica un usuario con nickname y password
     * 
     * @param $testCase Instancia del test (para hacer requests)
     * @param string $nickname
     * @param string $password
     * @return string Token de acceso (Bearer token)
     */
    public static function login($testCase, string $nickname, string $password): string
    {
        $response = $testCase->postJson('/api/auth/login', [
            'nickname' => $nickname,
            'password' => $password,
        ]);

        return $response->json('tokenAccess');
    }
}
