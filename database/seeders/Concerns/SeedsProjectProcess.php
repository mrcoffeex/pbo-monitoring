<?php

namespace Database\Seeders\Concerns;

use App\Models\Project;
use Carbon\CarbonInterface;
use Database\Seeders\Support\ProjectSeedData;
use Illuminate\Database\Eloquent\Model;

trait SeedsProjectProcess
{
    /**
     * @param  callable(Project, int, array<string, CarbonInterface|list<CarbonInterface>>): void  $callback
     */
    protected function forEachProject(callable $callback): void
    {
        $userId = ProjectSeedData::userId();

        foreach (ProjectSeedData::projects() as $project) {
            $callback($project, $userId, ProjectSeedData::timeline($project));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function stamp(Model $record, CarbonInterface $occurredAt): void
    {
        $record->forceFill([
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ])->save();
    }
}
