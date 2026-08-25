<?php

namespace Database\Factories;

use App\Models\ProcurementControl;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProcurementControl>
 */
class ProcurementControlFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'controlled_date' => fake()->dateTimeBetween('-15 months', '-1 month')->format('Y-m-d'),
            'abc' => number_format(fake()->randomFloat(2, 50_000, 20_000_000), 2, '.', ''),
            'remarks' => fake()->optional(0.7)->randomElement([
                'PMO controlled and endorsed to BAC.',
                'ABC verified against the approved allotment.',
                'Ready for invitation to bid.',
            ]),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
