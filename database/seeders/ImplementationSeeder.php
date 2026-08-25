<?php

namespace Database\Seeders;

use App\Models\Implementation;
use App\Models\Project;
use Database\Seeders\Concerns\SeedsProjectProcess;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Seeder;

class ImplementationSeeder extends Seeder
{
    use SeedsProjectProcess;

    public function run(): void
    {
        $this->forEachProject(function (Project $project, int $userId, array $timeline): void {
            if ($project->implementations()->exists()) {
                return;
            }

            $percentages = ProjectSeedData::implementationPercentages($project);
            $start = $timeline['impl_start'];
            $end = $timeline['impl_end'];
            $span = max(1, $start->diffInDays($end));
            $coordinates = sprintf('%.4f, %.4f', 14.5995 + (($project->id % 20) * 0.01), 120.9842 + (($project->id % 15) * 0.01));

            foreach ($percentages as $index => $percentage) {
                $inspectionDate = $start->copy()->addDays((int) round($span * (($index + 1) / count($percentages))));

                if ($inspectionDate->isFuture()) {
                    $inspectionDate = now()->copy();
                }

                $record = Implementation::factory()->create([
                    'project_id' => $project->id,
                    'user_id' => $userId,
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'date' => $inspectionDate->toDateString(),
                    'percentage' => $percentage,
                    'remarks' => $percentage >= 100
                        ? 'Final inspection completed. Project is substantially complete.'
                        : 'Progress inspection recorded at '.$percentage.'% accomplishment.',
                    'coordinates' => $coordinates,
                ]);

                $this->stamp($record, $inspectionDate);
            }
        });
    }
}
