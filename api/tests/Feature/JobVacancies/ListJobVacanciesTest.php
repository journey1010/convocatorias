<?php

namespace Tests\Feature\JobVacancies;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\JobVacancies\Models\{JobVacancy, JobProfile};
use Modules\JobVacancies\Enums\VacancyStatus;
use Modules\User\Models\User;
use Modules\Office\Models\{Locale, Office};

class ListJobVacanciesTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Locale $locale;
    protected Office $office;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locale = Locale::create(['name' => 'Test Locale']);
        $this->office = Office::create([
            'locale_id' => $this->locale->id,
            'name' => 'Test Office'
        ]);
        $this->adminUser = User::factory()->create();
        $this->token = 'Bearer test-admin-token';

        // Create test vacancies
        $this->createVacancies();
    }

    protected function createVacancies()
    {
        // Published vacancy
        JobVacancy::create([
            'user_id' => $this->adminUser->id,
            'locale_id' => $this->locale->id,
            'title' => 'Published Vacancy',
            'status' => VacancyStatus::PUBLICADA->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ]);

        // Closed vacancy (should not appear in public list)
        JobVacancy::create([
            'user_id' => $this->adminUser->id,
            'locale_id' => $this->locale->id,
            'title' => 'Closed Vacancy',
            'status' => VacancyStatus::CERRADA->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ]);

        // In evaluation vacancy (should not appear in public list)
        JobVacancy::create([
            'user_id' => $this->adminUser->id,
            'locale_id' => $this->locale->id,
            'title' => 'In Evaluation Vacancy',
            'status' => VacancyStatus::EN_EVALUACION->value,
            'mode' => false,
            'start_date' => '2026-03-01',
            'close_date' => '2026-03-31',
        ]);
    }

    /** @test */
    public function public_users_can_list_vacancies()
    {
        $response = $this->getJson('/api/job-vacancies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'items' => [
                    '*' => [
                        'id',
                        'title',
                        'status',
                        'start_date',
                        'close_date',
                        'profiles',
                        'files',
                    ],
                ],
            ]);
    }

    /** @test */
    public function public_list_shows_only_published_vacancies()
    {
        $response = $this->getJson('/api/job-vacancies');

        $response->assertStatus(200);
        
        $items = $response->json('items');
        
        // Should only show published vacancies
        $this->assertCount(1, $items);
        $this->assertEquals('Published Vacancy', $items[0]['title']);
    }

    /** @test */
    public function public_list_does_not_show_sensitive_data()
    {
        $response = $this->getJson('/api/job-vacancies');

        $response->assertStatus(200);
        
        $items = $response->json('items');
        
        // Public users should not see user_id and locale_id
        $this->assertArrayNotHasKey('user_id', $items[0]);
        $this->assertArrayNotHasKey('locale_id', $items[0]);
    }

    /** @test */
    public function admin_list_shows_all_vacancies()
    {
        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->getJson('/api/job-vacancies');

        $response->assertStatus(200);
        
        $items = $response->json('items');
        
        // Admins should see all vacancies in their locale
        $this->assertGreaterThanOrEqual(3, count($items));
    }

    /** @test */
    public function admin_list_shows_sensitive_data()
    {
        $response = $this->withHeaders([
            'Authorization' => $this->token,
        ])->getJson('/api/job-vacancies');

        $response->assertStatus(200);
        
        $items = $response->json('items');
        
        // Admins should see user_id and locale_id
        $this->assertArrayHasKey('user_id', $items[0]);
        $this->assertArrayHasKey('locale_id', $items[0]);
    }
}
