<?php

namespace Database\Factories;

use App\Models\Procurement;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procurement>
 */
class ProcurementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $conference = fake()->dateTimeBetween('-14 months', '-3 months');
        $preBid = (clone $conference)->modify('+'.fake()->numberBetween(7, 14).' days');
        $bidOpening = (clone $preBid)->modify('+'.fake()->numberBetween(7, 21).' days');
        $ber = (clone $bidOpening)->modify('+'.fake()->numberBetween(3, 10).' days');
        $postQua = (clone $ber)->modify('+'.fake()->numberBetween(2, 8).' days');
        $noa = (clone $postQua)->modify('+'.fake()->numberBetween(3, 12).' days');
        $ntp = (clone $noa)->modify('+'.fake()->numberBetween(5, 15).' days');

        return [
            'ib_number' => 'C-'.fake()->unique()->numerify('######'),
            'pre_procurement_conference' => $conference->format('Y-m-d'),
            'pre_bid_conference' => $preBid->format('Y-m-d'),
            'bid_opening' => [$bidOpening->format('Y-m-d')],
            'ber' => $ber->format('Y-m-d'),
            'post_qua_date' => $postQua->format('Y-m-d'),
            'remarks' => fake()->optional(0.7)->randomElement([
                'Single calculated responsive bid.',
                'Two bidders passed post-qualification.',
                'Awarded to the lowest calculated responsive bidder.',
            ]),
            'noa_date_received' => $noa->format('Y-m-d'),
            'contract_amount' => number_format(fake()->randomFloat(2, 80_000, 25_000_000), 2, '.', ''),
            'contractor' => fake()->randomElement([
                'Apex Builders Corporation',
                'Northern Star Construction',
                'Metro Pacific Contractors',
                'Greenfield Development Corp.',
                'Primeway Builders',
            ]),
            'ntp_number' => 'NTP-'.fake()->unique()->numerify('######'),
            'ntp_date' => $ntp->format('Y-m-d'),
            'contract_duration' => fake()->numberBetween(30, 240),
            'user_id' => User::query()->value('id') ?? User::factory(),
            'project_id' => Project::factory(),
        ];
    }
}
