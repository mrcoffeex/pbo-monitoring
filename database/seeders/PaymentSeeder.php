<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->payments()->exists()) {
                return;
            }

            $types = ProjectSeedData::paymentTypes($project);
            $contract = ProjectSeedData::amounts($project)['contract'];
            $shares = match (count($types)) {
                1 => [1.0],
                2 => [0.15, 0.85],
                3 => [0.15, 0.40, 0.45],
                default => [0.15, 0.25, 0.25, 0.35],
            };

            $allocated = 0.0;

            foreach ($types as $index => $type) {
                $isLast = $index === array_key_last($types);
                $amount = $isLast
                    ? round($contract - $allocated, 2)
                    : round($contract * $shares[$index], 2);
                $allocated += $amount;

                $paymentDate = $timeline['payments'][$index] ?? $timeline['impl_start']->copy()->addDays(($index + 1) * 20);
                $checkDate = $paymentDate->copy()->addDays(5);

                $record = Payment::factory()->create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'type' => $type,
                    'date' => $paymentDate->toDateString(),
                    'amount' => number_format($amount, 2, '.', ''),
                    'payable_reference' => ProjectSeedData::reference('APV', $project, $index + 1),
                    'payment_reference' => ProjectSeedData::reference('PV', $project, $index + 1),
                    'check_number' => sprintf('%08d', ($project->id * 10) + $index + 1),
                    'check_date' => $checkDate->toDateString(),
                ]);

                $this->stamp($record, $paymentDate);
            }
        });
    }
}
