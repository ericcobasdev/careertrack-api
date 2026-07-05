<?php

namespace Tests\Feature\JobApplications;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobApplicationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_name_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = $this->validPayload();
        unset($payload['company_name']);

        $response = $this->postJson('/api/applications', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('company_name');
    }

    public function test_position_title_is_required(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $payload = $this->validPayload();
        unset($payload['position_title']);

        $response = $this->postJson('/api/applications', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('position_title');
    }

    public function test_status_must_be_a_valid_application_status(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/applications', [
            ...$this->validPayload(),
            'status' => 'not_a_real_status',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_technical_test_status_is_allowed(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/applications', [
            ...$this->validPayload(),
            'status' => ApplicationStatus::TechnicalTest->value,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', ApplicationStatus::TechnicalTest->value);
    }

    public function test_salary_max_cannot_be_less_than_salary_min_when_both_are_present(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/applications', [
            ...$this->validPayload(),
            'salary_min' => 90000,
            'salary_max' => 70000,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_max');
    }

    public function test_next_step_at_cannot_be_before_applied_at_when_both_are_present(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/applications', [
            ...$this->validPayload(),
            'applied_at' => '2026-07-10',
            'next_step_at' => '2026-07-01 09:00:00',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('next_step_at');
    }

    public function test_patch_allows_partial_updates(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'Old Company',
            'position_title' => 'Backend Engineer',
        ]);

        $response = $this->patchJson("/api/applications/{$jobApplication->id}", [
            'company_name' => 'New Company',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.company_name', 'New Company')
            ->assertJsonPath('data.position_title', 'Backend Engineer');
    }

    public function test_patch_cannot_set_salary_max_below_existing_salary_min(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'salary_min' => 90000,
            'salary_max' => 100000,
        ]);

        $response = $this->patchJson("/api/applications/{$jobApplication->id}", [
            'salary_max' => 70000,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('salary_max');
    }

    public function test_patch_cannot_set_next_step_at_before_existing_applied_at(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'applied_at' => '2026-07-10',
            'next_step_at' => '2026-07-15 09:00:00',
        ]);

        $response = $this->patchJson("/api/applications/{$jobApplication->id}", [
            'next_step_at' => '2026-07-01 09:00:00',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('next_step_at');
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'company_name' => 'GitHub',
            'position_title' => 'Backend Engineer',
            'status' => ApplicationStatus::Applied->value,
            'source' => 'LinkedIn',
            'source_url' => 'https://www.linkedin.com/jobs/view/123',
            'salary_min' => 70000,
            'salary_max' => 90000,
            'location' => 'Remote',
            'notes' => 'Promising role.',
            'applied_at' => '2026-07-01',
            'next_step_at' => '2026-07-10 09:00:00',
        ];
    }
}
