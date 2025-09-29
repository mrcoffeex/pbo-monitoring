<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Js;

class FinancialStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Obligation vs Allotment Status';

    protected static ?string $maxHeight = '250px';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 3,
        ];
    }

    protected function getData(): array
    {
        // Get the selected filter (year)
        $selectedYear = $this->filter ?? now()->year;

        $projects = Project::with([
            'obligation_requests:id,project_id,amount',
        ])->where('year', $selectedYear)->get();

        $totalAllotment = (float) $projects->sum('allotment');
        $totalObligated = (float) $projects->sum(function ($project) {
            return $project->obligation_requests->sum('amount');
        });

        // Prevent division by zero
        if ($totalAllotment <= 0) {
            $totalAllotment = 1;
        }

        $obligatedAmount = min($totalObligated, $totalAllotment); // Cap at allotment
        $remainingAllotment = max($totalAllotment - $totalObligated, 0);

        $percentageObligated = round(($obligatedAmount / $totalAllotment) * 100, 1);
        $percentageRemaining = round(($remainingAllotment / $totalAllotment) * 100, 1);

        return [
            'datasets' => [
                [
                    'label' => 'Amount (%)',
                    'data' => [$percentageObligated, $percentageRemaining],
                    'backgroundColor' => [
                        '#f472b6', // obligated
                        '#dadee6', // remaining
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Obligated (₱" . number_format($obligatedAmount, 2) . " - {$percentageObligated}%)",
                "Remaining (₱" . number_format($remainingAllotment, 2) . " - {$percentageRemaining}%)",
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
        return 3;
    }
}
