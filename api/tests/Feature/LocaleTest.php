<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Helpers\UserHelper;
use Modules\Office\Models\Locale;

class LocaleTest extends TestCase
{
    use DatabaseTransactions;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $userData = UserHelper::create([], [], ['offices.manage']);
        $response = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => $userData['password'],
        ]);

        $this->token = $response->json('tokenAccess');
    }

    public function test_can_create_locale()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/office/locale/create', [
                'name' => 'Local de Prueba'
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('locales', ['name' => 'Local de Prueba']);
    }

    public function test_can_list_locales()
    {
        Locale::create(['name' => 'Local 1']);
        Locale::create(['name' => 'Local 2']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/office/locale/list');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_update_locale()
    {
        $locale = Locale::create(['name' => 'Local Antiguo']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson('/api/office/locale/update', [
                'id' => $locale->id,
                'name' => 'Local Actualizado'
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('locales', [
            'id' => $locale->id,
            'name' => 'Local Actualizado'
        ]);
    }
}
