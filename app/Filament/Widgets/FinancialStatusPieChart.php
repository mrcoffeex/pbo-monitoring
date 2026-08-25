<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasYearChartFilter;
use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class FinancialStatusPieChart extends ChartWidget
{
    use HasYearChartFilter;

    protected static ?string $heading = 'Obligation vs Allotment';

    protected static ?string $description = 'Share of allotment already obligated';

    protected static ?string $maxHeight = '280px';

    public function getColumnSpan(): int|string|array
    {
        return $this->halfWidthSpan();
    }

    protected function getData(): array
    {
        $selectedYear = $this->selectedYear();

        $projects = Project::query()
            ->with(['obligation_requests:id,project_id,amount'])
            ->where('year', $selectedYear)
            ->get(['id', 'allotment']);

        $totalAllotment = (float) $projects->sum('allotment');
        $totalObligated = (float) $projects->sum(
            fn (Project $project): float => (float) $project->obligation_requests->sum('amount'),
        );

        $obligatedAmount = $totalAllotment > 0
            ? min($totalObligated, $totalAllotment)
            : max($totalObligated, 0);
        $remainingAllotment = max($totalAllotment - $totalObligated, 0);

        return [
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => [$obligatedAmount, $remainingAllotment],
                    'backgroundColor' => [
                        '#ec4899',
                        '#cbd5e1',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                'Obligated',
                'Remaining',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        return $this->currencyDoughnutOptions();
    }

    public static function getSort(): int
    {
        return 2;
    }
}
