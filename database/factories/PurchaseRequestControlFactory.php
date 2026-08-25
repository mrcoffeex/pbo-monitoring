<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\PurchaseRequestControl;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseRequestControl>
 */
class PurchaseRequestControlFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'controlled_date' => fake()->dateTimeBetween('-14 months', '-1 month')->format('Y-m-d H:i:s'),
            'control_number' => 'CN-'.fake()->unique()->numerify('######'),
            'amount' => number_format(fake()->randomFloat(2, 50_000, 20_000_000), 2, '.', ''),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
