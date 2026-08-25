<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasYearChartFilter;
use App\Models\ObligationRequest;
use App\Models\Payment;
use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class FinancialOverviewBarChart extends ChartWidget
{
    use HasYearChartFilter;

    protected static ?string $heading = 'Financial Overview';

    protected static ?string $description = 'Appropriation, allotment, obligated, and disbursed amounts';

    protected static ?string $maxHeight = '280px';

    public function getColumnSpan(): int|string|array
    {
        return $this->halfWidthSpan();
    }

    protected function getData(): array
    {
        $selectedYear = $this->selectedYear();

        $projects = Project::query()->where('year', $selectedYear);

        $totalAppropriation = (float) (clone $projects)->sum('appropriation');
        $totalAllotment = (float) (clone $projects)->sum('allotment');
        $totalObligated = (float) ObligationRequest::query()
            ->whereHas('project', fn ($query) => $query->where('year', $selectedYear))
            ->sum('amount');
        $totalDisbursed = (float) Payment::query()
            ->whereHas('project', fn ($query) => $query->where('year', $selectedYear))
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => [
                        $totalAppropriation,
                        $totalAllotment,
                        $totalObligated,
                        $totalDisbursed,
                    ],
                    'backgroundColor' => [
                        '#ec4899',
                        '#06b6d4',
                        '#f59e0b',
                        '#10b981',
                    ],
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                    'maxBarThickness' => 48,
                ],
            ],
            'labels' => [
                'Appropriation',
                'Allotment',
                'Obligated',
                'Disbursed',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return $this->currencyAxisOptions();
    }

    public static function getSort(): int
    {
        return 1;
    }
}
