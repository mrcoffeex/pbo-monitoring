<?php

namespace App\Models;

use App\Enums\CustomOptions;
use App\Enums\ProcessStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTypeWorkflow extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectTypeWorkflowFactory> */
    use HasFactory;

    protected $fillable = [
        'project_type',
        'stages',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stages' => 'array',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function defaultStagesByType(): array
    {
        return [
            'infrastructure_project' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::TechnicalWorkingGroup->value,
                ProcessStage::ProcurementControl->value,
                ProcessStage::PurchaseRequestControl->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Implementation->value,
                ProcessStage::Payment->value,
            ],
            'non_infrastructure_project' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::TechnicalWorkingGroup->value,
                ProcessStage::ProcurementControl->value,
                ProcessStage::PurchaseRequestControl->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Payment->value,
            ],
            'land_project' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Implementation->value,
                ProcessStage::Payment->value,
            ],
            'Scholarship' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Payment->value,
            ],
            'Loans Payment' => [
                ProcessStage::ObligationRequest->value,
                ProcessStage::Payment->value,
            ],
            'Livelihood' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Payment->value,
            ],
            'Relocation Site' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::TechnicalWorkingGroup->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Implementation->value,
                ProcessStage::Payment->value,
            ],
            'Other' => [
                ProcessStage::PreProcurement->value,
                ProcessStage::PurchaseRequest->value,
                ProcessStage::Procurement->value,
                ProcessStage::ObligationRequest->value,
                ProcessStage::Payment->value,
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function stagesForType(?string $type): array
    {
        if (! filled($type)) {
            return self::defaultStagesByType()['infrastructure_project'];
        }

        $stored = self::query()->where('project_type', $type)->value('stages');

        if (is_array($stored) && $stored !== []) {
            return array_values(array_filter(
                $stored,
                fn (mixed $stage): bool => is_string($stage) && ProcessStage::tryFrom($stage) instanceof ProcessStage,
            ));
        }

        return self::defaultStagesByType()[$type]
            ?? self::defaultStagesByType()['infrastructure_project'];
    }

    public function typeLabel(): string
    {
        return CustomOptions::PROJECT_TYPES[$this->project_type] ?? $this->project_type;
    }

    /**
     * @return list<string>
     */
    public function stageValues(): array
    {
        return collect($this->stages ?? [])
            ->map(fn (mixed $stage): ?string => ProcessStage::fromStored($stage)?->value)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function stageLabels(): array
    {
        return collect($this->stageValues())
            ->map(fn (string $stage): string => ProcessStage::from($stage)->label())
            ->all();
    }
}
