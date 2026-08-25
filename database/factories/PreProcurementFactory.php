<?php

namespace Database\Factories;

use App\Models\PreProcurement;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PreProcurement>
 */
class PreProcurementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'remarks' => fake()->randomElement([
                'Market survey completed. Recommended to proceed with public bidding.',
                'End-user submitted complete technical specifications.',
                'Pre-procurement documents reviewed and found in order.',
                'Recommended alternative mode subject to BAC confirmation.',
            ]),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
