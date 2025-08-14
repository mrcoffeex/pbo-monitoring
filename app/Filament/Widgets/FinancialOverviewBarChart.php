<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Payment;
use App\Models\ObligationRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Js;

class FinancialOverviewBarChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Overview (Appropriation / Obligated / Disbursed)';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $totalAppropriation = (float) Project::sum('appropriation');
        $totalObligated     = (float) ObligationRequest::sum('amount');
        $totalDisbursed     = (float) Payment::sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Amount (PHP)',
                    'data' => [
                        $totalAppropriation,
                        $totalObligated,
                        $totalDisbursed,
                    ],
                    'backgroundColor' => [
                        '#2563eb', // Appropriation
                        '#f59e0b', // Obligated
                        '#10b981', // Disbursed
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                'Appropriation',
                'Obligated',
                'Disbursed',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    public static function getSort(): int
    {
        return 1;
    }
}
