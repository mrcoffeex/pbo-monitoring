<?php

namespace Database\Seeders;

use App\Models\PreProcurement;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Illuminate\Database\Seeder;

class PreProcurementSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->pre_procurements()->exists()) {
                return;
            }

            $record = PreProcurement::factory()->create([
                'project_id' => $project->id,
                'user_id' => $userId,
                'remarks' => 'Pre-procurement review completed for '.$project->code.'. Documents endorsed for purchase request.',
            ]);

            $this->stamp($record, $timeline['pre_procurement']);
        });
    }
}
