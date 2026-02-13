<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Helpers\UserHelper;
use Tests\Helpers\AuthenticationHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class PersonalDataExtraTest extends TestCase
{
    /**
     * DatabaseTransactions envuelve cada test en una transacción 
     * y hace rollback al finalizar. Es mucho más rápido que RefreshDatabase.
     */
    use DatabaseTransactions;

    /**
     * En lugar de setUpBeforeClass manual, Laravel recomienda usar
     * las herramientas de Artisan dentro de un entorno controlado.
     * Si necesitas migrar y seedear solo una vez, se hace en el script
     * de ejecución o mediante un flag. 
     */
    protected function setUp(): void
    {
        parent::setUp();
        
    }

    public function test_create_personal_data_extra_with_post(): void
    {
        // 1. Crear usuario postulante (Usando tu Helper)
        $credentials = UserHelper::createApplicant([
            'name' => 'Juan',
            'last_name' => 'Pérez',
        ]);

        // 2. Obtener Token (O usar actingAs si el middleware lo permite)
        $token = AuthenticationHelper::login(
            $this,
            $credentials['nickname'],
            $credentials['password']
        );

        $payload = [
            'department_id' => 1,
            'province_id' => 1,
            'district_id' => 1,
            'address' => 'Calle Principal 123, Apartamento 4B',
            'birthday' => '1990-05-15',
            'gender' => 1,                          
            'ruc' => '123456789',
            'have_cert_disability' => false,
            'have_cert_army' => false,
            'have_cert_professional_credentials' => false,
            'is_active_cert_professional_credentials' => false,
        ];

        // 3. Solicitud con las propiedades que tu middleware necesita
        // Inyectamos el 'sub' manualmente si el middleware lo requiere
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/accounts/personal-data', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                        'department_id',
                        'province_id',
                        'district_id',
                        'address',
                        'birthday',
                        'gender',
                        'have_cert_disability',
                        'file_cert_disability',
                        'have_cert_army',
                        'file_cert_army',
                        'have_cert_professional_credentials',
                        'file_cert_professional_credentials',
                        'is_active_cert_professional_credentials'
                 ]);
    }

    public function test_update_personal_data_extra_with_post(): void
    {
        $credentials = UserHelper::createApplicant();
        $token = AuthenticationHelper::login($this, $credentials['nickname'], $credentials['password']);

        // 1. Registro inicial
        DB::table('personal_data_extra')->insert([
            'user_id' => $credentials['user']->id,
            'department_id' => 1,
            'province_id' => 1,
            'district_id' => 1,
            'address' => 'Dirección inicial',
            'birthday' => '1990-05-15',
            'gender' => 1,
            'have_cert_disability' => false,
            'have_cert_army' => false,
            'have_cert_professional_credentials' => false,
            'is_active_cert_professional_credentials' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Preparamos el archivo Fake
        Storage::fake('private');
        $file = UploadedFile::fake()->create('certificado_militar.pdf', 500, 'application/pdf');

        // 3. Payload con archivo y Method Spoofing
        $updatedPayload = [
            'department_id' => 1,
            'province_id' => 2,
            'district_id' => 2,
            'address' => 'Dirección actualizada',
            'birthday' => '1990-05-15',
            'gender' => 2,
            'have_cert_disability' => false,
            'have_cert_army' => true,
            'file_cert_army' => $file, // <--- Enviamos el archivo
            'have_cert_professional_credentials' => false,
            'is_active_cert_professional_credentials' => false,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/accounts/personal-data', $updatedPayload);

        // 4. Verificaciones
        $response->assertStatus(201);
        
        // Verificar que los datos cambiaron en la BD
        $this->assertDatabaseHas('personal_data_extra', [
            'user_id' => $credentials['user']->id,
            'address' => 'Dirección actualizada',
            'have_cert_army' => true,
        ]);

        // Verificar que el archivo se guardó físicamente en el disco fake
        // Nota: Aquí debes poner la ruta que tu Service genera (ej: personal_data_certs/...)
        $personalData = \Modules\Accounts\Models\PersonalDataExtra::where('user_id', $credentials['user']->id)->first();
        
        /** @var Cloud $storage */
        $storage  = Storage::disk('private');
        $storage->assertExists($personalData->file_cert_army);
    }
}