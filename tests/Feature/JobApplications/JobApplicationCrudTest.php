<?php

namespace Tests\Feature\JobApplications;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobApplicationCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_job_application(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/applications', [
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
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.company_name', 'GitHub')
            ->assertJsonPath('data.position_title', 'Backend Engineer')
            ->assertJsonPath('data.status', ApplicationStatus::Applied->value)
            ->assertJsonMissingPath('data.user_id');

        $this->assertDatabaseHas('job_applications', [
            'user_id' => $user->id,
            'company_name' => 'GitHub',
            'position_title' => 'Backend Engineer',
        ]);
    }

    public function test_user_can_list_only_their_own_job_applications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $ownApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'GitHub',
            'position_title' => 'Backend Engineer',
        ]);

        $otherApplication = JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Stripe',
            'position_title' => 'Platform Engineer',
        ]);

        $response = $this->getJson('/api/applications');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.id', $ownApplication->id)
            ->assertJsonPath('data.0.company_name', 'GitHub')
            ->assertJsonMissingPath('data.0.user_id')
            ->assertJsonMissing([
                'id' => $otherApplication->id,
                'company_name' => 'Stripe',
            ]);
    }

    public function test_user_can_view_their_own_job_application(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'Shopify',
            'position_title' => 'API Engineer',
            'status' => ApplicationStatus::Interview->value,
        ]);

        $response = $this->getJson("/api/applications/{$jobApplication->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $jobApplication->id)
            ->assertJsonPath('data.company_name', 'Shopify')
            ->assertJsonPath('data.position_title', 'API Engineer')
            ->assertJsonPath('data.status', ApplicationStatus::Interview->value)
            ->assertJsonMissingPath('data.user_id');
    }

    public function test_user_cannot_view_another_users_job_application(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Microsoft',
        ]);

        $response = $this->getJson("/api/applications/{$jobApplication->id}");

        $response->assertForbidden();
    }

    public function test_user_can_update_their_own_job_application(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'Old Company',
            'position_title' => 'Backend Developer',
            'status' => ApplicationStatus::Applied->value,
        ]);

        $response = $this->patchJson("/api/applications/{$jobApplication->id}", [
            'company_name' => 'New Company',
            'position_title' => 'Senior Backend Developer',
            'status' => ApplicationStatus::Offer->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $jobApplication->id)
            ->assertJsonPath('data.company_name', 'New Company')
            ->assertJsonPath('data.position_title', 'Senior Backend Developer')
            ->assertJsonPath('data.status', ApplicationStatus::Offer->value)
            ->assertJsonMissingPath('data.user_id');

        $this->assertDatabaseHas('job_applications', [
            'id' => $jobApplication->id,
            'user_id' => $user->id,
            'company_name' => 'New Company',
            'position_title' => 'Senior Backend Developer',
        ]);
    }

    public function test_user_cannot_update_another_users_job_application(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Original Company',
        ]);

        $response = $this->patchJson("/api/applications/{$jobApplication->id}", [
            'company_name' => 'Unauthorized Update',
        ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('job_applications', [
            'id' => $jobApplication->id,
            'company_name' => 'Original Company',
        ]);
    }

    public function test_user_can_delete_their_own_job_application(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'GitLab',
        ]);

        $response = $this->deleteJson("/api/applications/{$jobApplication->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('job_applications', [
            'id' => $jobApplication->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_job_application(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $jobApplication = JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Laravel',
        ]);

        $response = $this->deleteJson("/api/applications/{$jobApplication->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('job_applications', [
            'id' => $jobApplication->id,
            'company_name' => 'Laravel',
        ]);
    }
}
