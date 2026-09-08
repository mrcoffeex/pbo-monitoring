<?php

namespace App\Enums;

enum ProcessStage: string
{
    case PreProcurement = 'pre_procurement';
    case PurchaseRequest = 'purchase_request';
    case TechnicalWorkingGroup = 'technical_working_group';
    case ProcurementControl = 'procurement_control';
    case PurchaseRequestControl = 'purchase_request_control';
    case Procurement = 'procurement';
    case ObligationRequest = 'obligation_request';
    case Implementation = 'implementation';
    case Payment = 'payment';

    public function label(): string
    {
        return match ($this) {
            self::PreProcurement => 'Pre-Procurement',
            self::PurchaseRequest => 'Purchase Requests',
            self::TechnicalWorkingGroup => 'TWG',
            self::ProcurementControl => 'PMO Control',
            self::PurchaseRequestControl => 'PR Control',
            self::Procurement => 'Procurement',
            self::ObligationRequest => 'Obligation',
            self::Implementation => 'Implementation',
            self::Payment => 'Payments',
        };
    }

    public function monitorKey(): string
    {
        return match ($this) {
            self::PreProcurement => 'pre',
            self::PurchaseRequest => 'pr',
            self::TechnicalWorkingGroup => 'twg',
            self::ProcurementControl => 'pmo',
            self::PurchaseRequestControl => 'prc',
            self::Procurement => 'proc',
            self::ObligationRequest => 'obr',
            self::Implementation => 'impl',
            self::Payment => 'pay',
        };
    }

    public function relationship(): string
    {
        return match ($this) {
            self::PreProcurement => 'pre_procurements',
            self::PurchaseRequest => 'purchase_requests',
            self::TechnicalWorkingGroup => 'technical_working_groups',
            self::ProcurementControl => 'procurement_controls',
            self::PurchaseRequestControl => 'purchase_request_controls',
            self::Procurement => 'procurements',
            self::ObligationRequest => 'obligation_requests',
            self::Implementation => 'implementations',
            self::Payment => 'payments',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PreProcurement => 'gray',
            self::PurchaseRequest => 'info',
            self::TechnicalWorkingGroup => 'warning',
            self::ProcurementControl => 'warning',
            self::PurchaseRequestControl => 'info',
            self::Procurement => 'primary',
            self::ObligationRequest => 'success',
            self::Implementation => 'success',
            self::Payment => 'success',
        };
    }

    public static function fromStored(mixed $stage): ?self
    {
        if (is_array($stage)) {
            $stage = $stage['stage'] ?? null;
        }

        return is_string($stage) ? self::tryFrom($stage) : null;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $stage): array => [$stage->value => $stage->label()])
            ->all();
    }
}
