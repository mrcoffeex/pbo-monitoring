<?php

namespace Database\Seeders;

use App\Models\ProcurementControl;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class ProcurementControlSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->procurement_controls()->exists()) {
                return;
            }

            $amounts = ProjectSeedData::amounts($project);

            $record = ProcurementControl::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'controlled_date' => $timeline['pmo_controlled']->toDateString(),
                'abc' => number_format($amounts['abc'], 2, '.', ''),
                'remarks' => 'PMO controlled the ABC and endorsed the project to BAC.',
            ]);

            $this->stamp($record, $timeline['pmo_controlled']);
        });
    }
}
