<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Helpers\UserHelper;
use Modules\Office\Models\Office;
use Modules\Office\Models\Locale;

class OfficeTest extends TestCase
{
    use DatabaseTransactions;

    protected $token;
    protected $locale;

    protected function setUp(): void
    {
        parent::setUp();

        $userData = UserHelper::create([], [], ['offices.manage']);
        $response = $this->postJson('/api/auth/login', [
            'nickname' => $userData['nickname'],
            'password' => $userData['password'],
        ]);

        $this->token = $response->json('tokenAccess');
        $this->locale = Locale::create(['name' => 'Sede Central']);
    }

    public function test_can_create_office_with_locale()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/office/create', [
                'name' => 'Oficina de Prueba',
                'locale_id' => $this->locale->id
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('offices', [
            'name' => 'Oficina de Prueba',
            'locale_id' => $this->locale->id
        ]);
    }

    public function test_can_update_office_with_locale()
    {
        $office = Office::create([
            'name' => 'Oficina Antigua',
            'locale_id' => $this->locale->id,
            'status' => 1,
            'level' => 1
        ]);

        $newLocale = Locale::create(['name' => 'Sede Secundaria']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->patchJson('/api/office/update', [
                'id' => $office->id,
                'name' => 'Oficina Actualizada',
                'status' => 1,
                'locale_id' => $newLocale->id
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('offices', [
            'id' => $office->id,
            'name' => 'Oficina Actualizada',
            'locale_id' => $newLocale->id
        ]);
    }
}
