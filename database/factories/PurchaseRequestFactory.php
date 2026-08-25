<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseRequest>
 */
class PurchaseRequestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $receivedDate = fake()->dateTimeBetween('-18 months', '-2 months');
        $forwardDate = (clone $receivedDate)->modify('+'.fake()->numberBetween(1, 10).' days');

        return [
            'received_date' => $receivedDate->format('Y-m-d H:i:s'),
            'pr_number' => 'PR-'.fake()->unique()->numerify('########'),
            'remarks' => fake()->optional(0.8)->randomElement([
                'PR received with complete attachments.',
                'Returned once for signature correction, now complete.',
                'Charged against the approved AIP.',
            ]),
            'forward_twg_date' => $forwardDate->format('Y-m-d H:i:s'),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
