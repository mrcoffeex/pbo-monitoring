<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\TechnicalWorkingGroup;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class TechnicalWorkingGroupSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->technical_working_groups()->exists()) {
                return;
            }

            $amounts = ProjectSeedData::amounts($project);

            $record = TechnicalWorkingGroup::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'review_date' => $timeline['twg_review']->format('Y-m-d H:i:s'),
                'review_remarks' => 'TWG reviewed the program of works and technical specifications.',
                'controlled_date' => $timeline['twg_controlled']->format('Y-m-d H:i:s'),
                'abc' => number_format($amounts['abc'], 2, '.', ''),
                'remarks' => 'Recommended for PMO control and bidding.',
            ]);

            $this->stamp($record, $timeline['twg_review']);
        });
    }
}
