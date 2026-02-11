<?php

namespace Tests\Feature\JobVacancies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\JobVacancies\Models\JobVacancy;
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\User\Models\User;
use Modules\Office\Models\Locale;

class UpdateJobVacancyTest extends TestCase
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

        // Create a vacancy
        $this->vacancy = JobVacancy::create([
            'user_id' => $this->adminUser->id,
            'locale_id' => $this->locale->id,
            'title' => 'Original Title',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ]);
    }

    /** @test */
    public function it_can_update_vacancy_when_status_is_publicada()
    {
        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('job_vacancies', [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function it_creates_no_edit_log_when_status_is_publicada()
    {
        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies', $data);

        // No logs should be created when status is PUBLICADA
        $this->assertDatabaseCount('job_vacancy_edit_logs', 0);
    }

    /** @test */
    public function it_creates_edit_log_when_status_is_not_publicada()
    {
        // Change status to EN_EVALUACION
        $this->vacancy->update(['status' => VacancyStatus::EN_EVALUACION->value]);

        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies', $data);

        // Log should be created when status is not PUBLICADA
        $this->assertDatabaseCount('job_vacancy_edit_logs', 1);
    }

    /** @test */
    public function it_cannot_update_when_status_is_en_evaluacion()
    {
        $this->vacancy->update(['status' => VacancyStatus::EN_EVALUACION->value]);

        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_cannot_update_when_status_is_finalizada()
    {
        $this->vacancy->update(['status' => VacancyStatus::FINALIZADA->value]);

        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->patchJson('/api/job-vacancies', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $data = [
            'id' => $this->vacancy->id,
            'title' => 'Updated Title',
        ];

        $response = $this->patchJson('/api/job-vacancies', $data);

        $response->assertStatus(401);
    }
}
