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
        // Get the selected filter (year)
        $selectedYear = $this->filter ?? now()->year;

        // Filter data based on selected year
        $totalAppropriation = (float) Project::where('year', $selectedYear)
            ->sum('appropriation');
        $totalObligated     = (float) ObligationRequest::whereHas('project', function($query) use ($selectedYear) {
            $query->where('year', $selectedYear);
        })->sum('amount');
        $totalDisbursed     = (float) Payment::whereHas('project', function($query) use ($selectedYear) {
            $query->where('year', $selectedYear);
        })->sum('amount');

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

    protected function getFilters(): ?array
    {
        // Get distinct years from projects
        $projectYears = Project::distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $filters = [];
        foreach ($projectYears as $year) {
            $filters[(string) $year] = (string) $year;
        }

        return $filters;
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
