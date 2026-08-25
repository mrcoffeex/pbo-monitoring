<?php

namespace Database\Factories;

use App\Enums\CustomOptions;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected static int $sequence = 1;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sequence = static::$sequence++;
        $year = (string) fake()->numberBetween(now()->year - 5, now()->year);
        $appropriation = fake()->randomFloat(2, 100_000, 50_000_000);
        $allotment = round($appropriation * fake()->randomFloat(2, 0.5, 1), 2);

        return [
            'type' => [fake()->randomElement(array_keys(CustomOptions::PROJECT_TYPES))],
            'code' => sprintf('RC%04d%04d', (int) $year % 10000, $sequence % 10000),
            'name' => sprintf(
                '%s - %s #%d',
                fake()->randomElement([
                    'Construction of',
                    'Rehabilitation of',
                    'Improvement of',
                    'Procurement of',
                    'Development of',
                ]),
                fake()->unique()->words(3, true),
                $sequence
            ),
            'year' => $year,
            'funds' => fake()->randomElements(
                array_keys(CustomOptions::FUNDS),
                fake()->numberBetween(1, 3)
            ),
            'appropriation' => number_format($appropriation, 2, '.', ''),
            'allotment' => number_format($allotment, 2, '.', ''),
            'status' => fake()->randomElement(array_keys(CustomOptions::PROJECT_STATUS)),
            'user_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
