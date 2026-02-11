<?php

namespace Tests\Feature\JobVacancies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\JobVacancies\Models\JobVacancy;
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\User\Models\User;
use Modules\Office\Models\Locale;

class JobVacancyStatusTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Locale $locale;
    protected JobVacancy $vacancy;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locale = Locale::create(['name' => 'Test Locale']);
        $this->adminUser = User::factory()->create();
        $this->token = 'Bearer test-admin-token';

        $this->vacancy = JobVacancy::create([
            'user_id' => $this->adminUser->id,
            'locale_id' => $this->locale->id,
            'title' => 'Test Vacancy',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ]);
    }

    /** @test */
    public function it_can_change_status_from_publicada_to_en_evaluacion()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::EN_EVALUACION->value,
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies/status', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_vacancies', [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::EN_EVALUACION->value,
        ]);
    }

    /** @test */
    public function it_logs_status_changes()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::EN_EVALUACION->value,
        ];

        $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies/status', $data);

        // Status changes should always be logged
        $this->assertDatabaseHas('job_vacancy_edit_logs', [
            'job_vacancy_id' => $this->vacancy->id,
            'action' => 'status_changed',
        ]);
    }

    /** @test */
    public function it_can_change_to_finalizada()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::FINALIZADA->value,
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies/status', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_vacancies', [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::FINALIZADA->value,
        ]);
    }

    /** @test */
    public function it_can_change_to_cancelada()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::CANCELADA->value,
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies/status', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_vacancies', [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::CANCELADA->value,
        ]);
    }

    /** @test */
    public function it_requires_authentication_to_change_status()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => VacancyStatus::EN_EVALUACION->value,
        ];

        $response = $this->patchJson('/api/job-vacancies/status', $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_status_value()
    {
        $data = [
            'id' => $this->vacancy->id,
            'status' => 999, // Invalid status
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies/status', $data);

        $response->assertStatus(422);
    }
}
