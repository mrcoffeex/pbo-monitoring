<?php

namespace Database\Factories;

use App\Enums\ProcessStage;
use App\Models\ProjectTypeWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTypeWorkflow>
 */
class ProjectTypeWorkflowFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_type' => 'land_project',
            'stages' => ProjectTypeWorkflow::defaultStagesByType()['land_project']
                ?? [
                    ProcessStage::PreProcurement->value,
                    ProcessStage::PurchaseRequest->value,
                    ProcessStage::Payment->value,
                ],
        ];
    }
}
