<?php

namespace Database\Factories;

use App\Enums\CustomOptions;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentDate = fake()->dateTimeBetween('-2 years', 'now');
        $checkDate = (clone $paymentDate)->modify('+'.fake()->numberBetween(0, 14).' days');

        return [
            'type' => fake()->randomElement(array_keys(CustomOptions::PAYMENTS)),
            'date' => $paymentDate->format('Y-m-d'),
            'amount' => number_format(fake()->randomFloat(2, 5_000, 5_000_000), 2, '.', ''),
            'payable_reference' => 'PR-'.fake()->unique()->numerify('########'),
            'payment_reference' => 'PV-'.fake()->unique()->numerify('########'),
            'check_number' => fake()->optional(0.7)->numerify('########'),
            'check_date' => fake()->boolean(70) ? $checkDate->format('Y-m-d') : null,
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
