<?php

namespace App\Models\Concerns;

use App\Enums\ProcessStage;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

trait EnforcesProjectWorkflow
{
    abstract public static function workflowStage(): ProcessStage;

    protected static function bootEnforcesProjectWorkflow(): void
    {
        static::creating(function (Model $model): void {
            $project = $model->project ?? Project::query()->find($model->project_id);

            if (! $project instanceof Project) {
                return;
            }

            $stage = static::workflowStage();

            if ($project->canEnterStage($stage)) {
                return;
            }

            throw ValidationException::withMessages([
                'project_id' => $project->stageBlockReason($stage) ?? 'This process is not available for the selected project.',
            ]);
        });
    }
}
