<?php

namespace Tests\Feature\JobVacancies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\JobVacancies\Models\{JobVacancy, JobProfile};
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\User\Models\User;
use Modules\Office\Models\{Locale, Office};

class JobProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Locale $locale;
    protected Office $office;
    protected JobVacancy $vacancy;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->locale = Locale::create(['name' => 'Test Locale']);
        $this->office = Office::create([
            'locale_id' => $this->locale->id,
            'name' => 'Test Office'
        ]);
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
    public function it_can_add_profiles_to_vacancy()
    {
        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [
                [
                    'title' => 'Desarrollador Backend',
                    'salary' => '3500',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-BE-001',
                ],
                [
                    'title' => 'Desarrollador Frontend',
                    'salary' => '3000',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-FE-001',
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(200)
            ->assertJsonCount(2, 'profiles');

        $this->assertDatabaseCount('job_profiles', 2);
    }

    /** @test */
    public function it_can_upload_profile_description_file()
    {
        $file = UploadedFile::fake()->create('profile_description.pdf', 100);

        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [
                [
                    'title' => 'Desarrollador Backend',
                    'salary' => '3500',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-BE-001',
                    'file' => $file,
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(200);

        // Verify file was stored
        $profile = JobProfile::first();
        $this->assertNotEmpty($profile->file);
    }

    /** @test */
    public function it_cannot_add_profiles_when_status_is_en_evaluacion()
    {
        $this->vacancy->update(['status' => VacancyStatus::EN_EVALUACION->value]);

        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [
                [
                    'title' => 'Desarrollador Backend',
                    'salary' => '3500',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-BE-001',
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_requires_at_least_one_profile()
    {
        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['profiles']);
    }

    /** @test */
    public function it_validates_profile_required_fields()
    {
        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [
                [
                    // Missing required fields
                    'code_profile' => 'DEV-001',
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'profiles.0.title',
                'profiles.0.salary',
                'profiles.0.office_id',
            ]);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $data = [
            'job_vacancy_id' => $this->vacancy->id,
            'profiles' => [
                [
                    'title' => 'Desarrollador Backend',
                    'salary' => '3500',
                    'office_id' => $this->office->id,
                    'code_profile' => 'DEV-BE-001',
                ],
            ],
        ];

        $response = $this->postJson('/api/job-vacancies/profiles', $data);

        $response->assertStatus(401);
    }
}
