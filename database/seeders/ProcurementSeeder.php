<?php

namespace Database\Seeders;

use App\Models\Procurement;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class ProcurementSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->procurements()->exists()) {
                return;
            }

            $amounts = ProjectSeedData::amounts($project);
            $secondOpening = $timeline['bid_opening']->copy()->addDays(7);

            $record = Procurement::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'ib_number' => ProjectSeedData::reference('C', $project),
                'pre_procurement_conference' => $timeline['pre_proc_conference']->toDateString(),
                'pre_bid_conference' => $timeline['pre_bid']->toDateString(),
                'bid_opening' => [
                    $timeline['bid_opening']->toDateString(),
                    $secondOpening->toDateString(),
                ],
                'ber' => $timeline['ber']->toDateString(),
                'post_qua_date' => $timeline['post_qua']->toDateString(),
                'remarks' => 'Awarded to the lowest calculated responsive bidder.',
                'noa_date_received' => $timeline['noa']->toDateString(),
                'contract_amount' => number_format($amounts['contract'], 2, '.', ''),
                'contractor' => ProjectSeedData::contractor($project),
                'ntp_number' => ProjectSeedData::reference('NTP', $project),
                'ntp_date' => $timeline['ntp']->toDateString(),
                'contract_duration' => $timeline['impl_start']->diffInDays($timeline['impl_end']) ?: 90,
            ]);

            $this->stamp($record, $timeline['pre_proc_conference']);
        });
    }
}
