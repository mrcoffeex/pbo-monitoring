<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\PurchaseRequestControl;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class PurchaseRequestControlSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->purchase_request_controls()->exists()) {
                return;
            }

            $amounts = ProjectSeedData::amounts($project);

            $record = PurchaseRequestControl::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'controlled_date' => $timeline['prc_controlled']->format('Y-m-d H:i:s'),
                'control_number' => ProjectSeedData::reference('CN', $project),
                'amount' => number_format($amounts['abc'], 2, '.', ''),
            ]);

            $this->stamp($record, $timeline['prc_controlled']);
        });
    }
}
