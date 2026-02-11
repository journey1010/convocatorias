<?php

namespace Tests\Feature\JobVacancies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\JobVacancies\Models\{JobVacancy, JobProfile};
use Modules\User\Models\User;
use Modules\Office\Models\{Locale, Office};
use Modules\JobVacancies\Enums\VacancyStatus;

class CreateJobVacancyTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Locale $locale;
    protected Office $office;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create locale
        $this->locale = Locale::create(['name' => 'Test Locale']);

        // Create office
        $this->office = Office::create([
            'locale_id' => $this->locale->id,
            'name' => 'Test Office'
        ]);

        // Create admin user
        $this->adminUser = User::factory()->create();
        
        // Generate auth token (adjust based on your auth implementation)
        // This is a placeholder - adjust based on your actual JWT implementation
        $this->token = 'Bearer test-admin-token';
    }

    /** @test */
    public function it_can_create_a_job_vacancy_with_profiles()
    {
        $data = [
            'locale_id' => $this->locale->id,
            'title' => 'Convocatoria de Prueba',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false, // Single profile mode
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
            'profiles' => [
                [
                    'title' => 'Desarrollador PHP',
                    'salary' => '3000',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-001',
                ],
                [
                    'title' => 'Diseñador UX',
                    'salary' => '2500',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DES-001',
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'status',
                'mode',
                'start_date',
                'close_date',
                'profiles',
            ]);

        $this->assertDatabaseHas('job_vacancies', [
            'title' => 'Convocatoria de Prueba',
            'status' => VacancyStatus::PUBLICADA->value,
        ]);

        $this->assertDatabaseCount('job_profiles', 2);
    }

    /** @test */
    public function it_requires_authentication_to_create_vacancy()
    {
        $data = [
            'title' => 'Test Vacancy',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ];

        $response = $this->postJson('/api/job-vacancies', $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'mode', 'start_date', 'close_date']);
    }

    /** @test */
    public function it_validates_close_date_is_after_start_date()
    {
        $data = [
            'locale_id' => $this->locale->id,
            'title' => 'Test Vacancy',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false,
            'start_date' => '2026-03-31',
            'close_date' => '2026-03-01', // Before start date
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['close_date']);
    }
}
