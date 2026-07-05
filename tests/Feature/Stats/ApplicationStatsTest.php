<?php

namespace Tests\Feature\Stats;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApplicationStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_stats_include_only_authenticated_users_applications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->count(2)->for($user)->create([
            'status' => ApplicationStatus::Applied->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->count(3)->for($otherUser)->create([
            'status' => ApplicationStatus::Applied->value,
            'next_step_at' => '2035-01-15 09:00:00',
        ]);

        $response = $this->getJson('/api/stats');

        $response
            ->assertOk()
            ->assertJsonPath('data.total', 2)
            ->assertJsonPath('data.by_status.applied', 2)
            ->assertJsonPath('data.upcoming_next_steps', 0);
    }

    public function test_stats_do_not_include_other_users_data(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Interview->value,
            'next_step_at' => '2035-02-01 10:00:00',
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'status' => ApplicationStatus::Offer->value,
            'next_step_at' => '2035-02-02 10:00:00',
        ]);

        $response = $this->getJson('/api/stats');

        $response
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.by_status.interview', 1)
            ->assertJsonPath('data.by_status.offer', 0)
            ->assertJsonPath('data.upcoming_next_steps', 1);
    }

    public function test_stats_count_applications_by_status(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->count(2)->for($user)->create([
            'status' => ApplicationStatus::Applied->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Interview->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::TechnicalTest->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Offer->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'status' => ApplicationStatus::Rejected->value,
            'next_step_at' => null,
        ]);

        $response = $this->getJson('/api/stats');

        $response
            ->assertOk()
            ->assertJsonPath('data.total', 5)
            ->assertJsonPath('data.by_status.applied', 2)
            ->assertJsonPath('data.by_status.interview', 1)
            ->assertJsonPath('data.by_status.technical_test', 1)
            ->assertJsonPath('data.by_status.offer', 1)
            ->assertJsonPath('data.by_status.rejected', 0);
    }

    public function test_stats_count_upcoming_next_steps(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Applied->value,
            'next_step_at' => '2035-03-01 09:00:00',
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Interview->value,
            'next_step_at' => '2035-03-02 09:00:00',
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Rejected->value,
            'next_step_at' => '2020-03-01 09:00:00',
        ]);

        JobApplication::factory()->for($user)->create([
            'status' => ApplicationStatus::Offer->value,
            'next_step_at' => null,
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'status' => ApplicationStatus::Applied->value,
            'next_step_at' => '2035-03-03 09:00:00',
        ]);

        $response = $this->getJson('/api/stats');

        $response
            ->assertOk()
            ->assertJsonPath('data.total', 4)
            ->assertJsonPath('data.upcoming_next_steps', 2);
    }
}
