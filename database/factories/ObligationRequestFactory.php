<?php

namespace Database\Factories;

use App\Models\ObligationRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ObligationRequest>
 */
class ObligationRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'controlled_date' => fake()->dateTimeBetween('-12 months', 'now')->format('Y-m-d'),
            'number' => 'OBR-'.fake()->unique()->numerify('######'),
            'amount' => number_format(fake()->randomFloat(2, 50_000, 20_000_000), 2, '.', ''),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
