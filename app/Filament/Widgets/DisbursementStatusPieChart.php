<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class DisbursementStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Status (Disbursed vs Not Disbursed)';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Load only what we need
        $projects = Project::with([
            'procurements:id,project_id,contract_amount',
            'payments:id,project_id,amount',
        ])->get();

        // Aggregate totals across ALL projects
        $totalContract = 0.0;
        $totalPaid     = 0.0;

        foreach ($projects as $project) {
            $totalContract += (float) $project->procurements->sum('contract_amount');
            $totalPaid     += (float) $project->payments->sum('amount');
        }

        // Use contract totals if available, else fall back to ABC
        $baseTotal = $totalContract ?? 0;

        // If still zero, nothing to show
        if ($baseTotal <= 0) {
            $disbursedAmount = 0;
            $notDisbursedAmount = 0;
            $disbursedPct = 0;
            $notDisbursedPct = 0;
        } else {
            $disbursedAmount = min($totalPaid, $baseTotal);
            $notDisbursedAmount = max($baseTotal - $disbursedAmount, 0);

            $disbursedPct = round(($disbursedAmount / $baseTotal) * 100, 1);
            $notDisbursedPct = round(($notDisbursedAmount / $baseTotal) * 100, 1);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Amount (PHP)',
                    'data' => [
                        $disbursedAmount,
                        $notDisbursedAmount,
                    ],
                    'backgroundColor' => [
                        '#4ade80', // disbursed
                        '#dadee6', // not disbursed
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Disbursed ({$disbursedPct}%)",
                "Not Disbursed ({$notDisbursedPct}%)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'padding' => 25, // space between legend items and chart
                    ],
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }

    public static function getSort(): int
    {
        return 4;
    }

}
