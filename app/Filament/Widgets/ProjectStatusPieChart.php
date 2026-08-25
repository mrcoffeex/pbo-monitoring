<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasYearChartFilter;
use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class ProjectStatusPieChart extends ChartWidget
{
    use HasYearChartFilter;

    protected static ?string $heading = 'Project Progress';

    protected static ?string $description = 'Completed, ongoing, and not started projects';

    protected static ?string $maxHeight = '280px';

    public function getColumnSpan(): int|string|array
    {
        return $this->halfWidthSpan();
    }

    protected function getData(): array
    {
        $selectedYear = $this->selectedYear();

        $projects = Project::query()
            ->with([
                'purchase_requests:id,project_id',
                'procurements:id,project_id,contract_amount',
                'payments:id,project_id,amount',
            ])
            ->where('year', $selectedYear)
            ->get(['id']);

        $completed = 0;
        $ongoing = 0;
        $notStarted = 0;

        foreach ($projects as $project) {
            $contractAmount = (float) $project->procurements->sum('contract_amount');
            $paidTotal = (float) $project->payments->sum('amount');
            $isCompleted = $contractAmount > 0 && $paidTotal >= $contractAmount;

            if ($isCompleted) {
                $completed++;

                continue;
            }

            if ($project->purchase_requests->isNotEmpty()) {
                $ongoing++;

                continue;
            }

            $notStarted++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [$completed, $ongoing, $notStarted],
                    'backgroundColor' => [
                        '#22c55e',
                        '#3b82f6',
                        '#cbd5e1',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 6,
                ],
            ],
            'labels' => [
                "Completed ({$completed})",
                "Ongoing ({$ongoing})",
                "Not Started ({$notStarted})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array|RawJs|null
    {
        return $this->doughnutOptions();
    }

    public static function getSort(): int
    {
        return 4;
    }
}
