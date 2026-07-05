<?php

namespace Tests\Feature\JobApplications;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobApplicationFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_filter_applications_by_status(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $matchingApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'GitHub',
            'status' => ApplicationStatus::Interview->value,
        ]);

        JobApplication::factory()->for($user)->create([
            'company_name' => 'Stripe',
            'status' => ApplicationStatus::Rejected->value,
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Microsoft',
            'status' => ApplicationStatus::Interview->value,
        ]);

        $response = $this->getJson('/api/applications?status='.ApplicationStatus::Interview->value);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingApplication->id)
            ->assertJsonPath('data.0.company_name', 'GitHub')
            ->assertJsonPath('data.0.status', ApplicationStatus::Interview->value);
    }

    public function test_user_can_filter_applications_by_company(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $matchingApplication = JobApplication::factory()->for($user)->create([
            'company_name' => 'Acme Labs',
        ]);

        JobApplication::factory()->for($user)->create([
            'company_name' => 'Beta Systems',
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Acme Labs',
        ]);

        $response = $this->getJson('/api/applications?company=Acme');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matchingApplication->id)
            ->assertJsonPath('data.0.company_name', 'Acme Labs');
    }

    public function test_user_can_sort_applications(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->for($user)->create([
            'company_name' => 'Charlie Company',
        ]);

        JobApplication::factory()->for($user)->create([
            'company_name' => 'Alpha Company',
        ]);

        JobApplication::factory()->for($user)->create([
            'company_name' => 'Bravo Company',
        ]);

        JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Aardvark Company',
        ]);

        $response = $this->getJson('/api/applications?sort_by=company_name&sort_direction=asc');

        $response
            ->assertOk()
            ->assertJsonPath('data.0.company_name', 'Alpha Company')
            ->assertJsonPath('data.1.company_name', 'Bravo Company')
            ->assertJsonPath('data.2.company_name', 'Charlie Company')
            ->assertJsonCount(3, 'data');
    }

    public function test_applications_are_paginated(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($user);

        JobApplication::factory()->count(3)->for($user)->sequence(
            ['company_name' => 'Company A'],
            ['company_name' => 'Company B'],
            ['company_name' => 'Company C'],
        )->create();

        JobApplication::factory()->for($otherUser)->create([
            'company_name' => 'Other User Company',
        ]);

        $response = $this->getJson('/api/applications?per_page=2&sort_by=company_name&sort_direction=asc');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.company_name', 'Company A')
            ->assertJsonPath('data.1.company_name', 'Company B')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonPath('links.prev', null);

        $this->assertNotNull($response->json('links.next'));
    }

    public function test_per_page_is_limited_by_validation(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/applications?per_page=101');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_invalid_sort_by_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/applications?sort_by=id;drop table users');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_by');
    }

    public function test_invalid_sort_direction_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/applications?sort_by=company_name&sort_direction=sideways');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_direction');
    }
}
