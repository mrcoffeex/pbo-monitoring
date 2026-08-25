<?php

namespace Database\Factories;

use App\Models\Implementation;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Implementation>
 */
class ImplementationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-12 months', '-2 months');
        $endDate = (clone $startDate)->modify('+'.fake()->numberBetween(45, 180).' days');
        $now = new \DateTime;
        $inspectionUntil = $endDate < $now ? $endDate : $now;
        $inspectionDate = fake()->dateTimeBetween($startDate, $inspectionUntil);

        return [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'date' => $inspectionDate->format('Y-m-d'),
            'percentage' => fake()->randomElement([15, 25, 40, 50, 65, 75, 90, 100]),
            'remarks' => fake()->optional(0.8)->randomElement([
                'On-site inspection completed. Works are on schedule.',
                'Slight delay due to weather. Catch-up plan submitted.',
                'Punch list items remaining before final acceptance.',
                'Accomplishment verified against the program of works.',
            ]),
            'coordinates' => sprintf(
                '%.4f, %.4f',
                fake()->latitude(6, 18),
                fake()->longitude(119, 126)
            ),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
