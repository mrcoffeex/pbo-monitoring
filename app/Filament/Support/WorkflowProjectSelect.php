<?php

namespace App\Filament\Support;

use App\Enums\ProcessStage;
use App\Models\Project;
use Closure;

class WorkflowProjectSelect
{
    public static function rule(ProcessStage $stage): Closure
    {
        return fn (): Closure => function (string $attribute, mixed $value, Closure $fail) use ($stage): void {
            $project = Project::query()->find($value);

            if (! $project instanceof Project) {
                return;
            }

            if ($project->canEnterStage($stage)) {
                return;
            }

            $fail($project->stageBlockReason($stage) ?? 'This process is not available for the selected project.');
        };
    }
}
