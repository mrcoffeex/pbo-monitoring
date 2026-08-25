<?php

namespace Database\Seeders;

use App\Models\ObligationRequest;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class ObligationRequestSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->obligation_requests()->exists()) {
                return;
            }

            $amounts = ProjectSeedData::amounts($project);

            $record = ObligationRequest::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'controlled_date' => $timeline['obr']->toDateString(),
                'number' => ProjectSeedData::reference('OBR', $project),
                'amount' => number_format($amounts['obligated'], 2, '.', ''),
            ]);

            $this->stamp($record, $timeline['obr']);
        });
    }
}
