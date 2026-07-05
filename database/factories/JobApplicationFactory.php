<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $salaryMin = fake()->optional()->numberBetween(30000, 80000);

        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'position_title' => fake()->jobTitle(),
            'status' => fake()->randomElement(ApplicationStatus::values()),
            'source' => fake()->randomElement(['LinkedIn', 'Indeed', 'Company website', 'Referral']),
            'source_url' => fake()->optional()->url(),
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMin ? fake()->numberBetween($salaryMin, $salaryMin + 30000) : null,
            'location' => fake()->randomElement(['Remote', 'Hybrid', fake()->city()]),
            'notes' => fake()->optional()->sentence(),
            'applied_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'next_step_at' => fake()->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
