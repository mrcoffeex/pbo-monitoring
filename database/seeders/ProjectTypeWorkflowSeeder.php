<?php

namespace Database\Seeders;

use App\Models\ProjectTypeWorkflow;
use Illuminate\Database\Seeder;

class ProjectTypeWorkflowSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ProjectTypeWorkflow::defaultStagesByType() as $type => $stages) {
            ProjectTypeWorkflow::query()->updateOrCreate(
                ['project_type' => $type],
                ['stages' => $stages],
            );
        }
    }
}
