<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\TechnicalWorkingGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TechnicalWorkingGroup>
 */
class TechnicalWorkingGroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $reviewDate = fake()->dateTimeBetween('-16 months', '-1 month');
        $controlledDate = (clone $reviewDate)->modify('+'.fake()->numberBetween(2, 10).' days');

        return [
            'review_date' => $reviewDate->format('Y-m-d H:i:s'),
            'review_remarks' => fake()->randomElement([
                'Specifications are consistent with the approved program of works.',
                'TWG recommends revision of quantity estimates before posting.',
                'Documents are complete and ready for control.',
            ]),
            'controlled_date' => $controlledDate->format('Y-m-d H:i:s'),
            'abc' => number_format(fake()->randomFloat(2, 50_000, 20_000_000), 2, '.', ''),
            'remarks' => fake()->optional(0.7)->sentence(),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
