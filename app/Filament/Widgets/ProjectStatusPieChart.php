<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Js;

class ProjectStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Project Status Distribution';

    protected static ?string $maxHeight = '250px';

    private array $statusCounts = [];

    protected function getData(): array
    {
        // Get the selected filter (year)
        $selectedYear = $this->filter ?? now()->year;

        $projects = Project::with([
            'purchase_requests:id,project_id',
            'procurements:id,project_id,contract_amount',
            'payments:id,project_id,amount',
        ])->where('year', $selectedYear)->get();

        $completed = 0;
        $ongoing = 0;
        $notStarted = 0;

        $projects->each(function ($project) use (&$completed, &$ongoing, &$notStarted) {
            $hasPR = $project->purchase_requests->isNotEmpty();
            $contractAmount = $project->procurements->sum('contract_amount');
            $paidTotal = $project->payments->sum('amount');
            $isCompleted = $contractAmount > 0 && $paidTotal >= $contractAmount;

            if ($isCompleted) { $completed++; return; }
            if ($hasPR) { $ongoing++; return; }
            $notStarted++;
        });

        $total = max($completed + $ongoing + $notStarted, 1);

        $pCompleted  = round(($completed / $total) * 100, 1);
        $pOngoing    = round(($ongoing / $total) * 100, 1);
        $pNotStarted = round(($notStarted / $total) * 100, 1);

        $this->statusCounts = [
            'completed'   => $completed,
            'ongoing'     => $ongoing,
            'notStarted'  => $notStarted,
            'pCompleted'  => $pCompleted,
            'pOngoing'    => $pOngoing,
            'pNotStarted' => $pNotStarted,
            'total'       => $total,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Projects (%)',
                    'data' => [$pCompleted, $pOngoing, $pNotStarted], // percentages
                    'rawCounts' => [$completed, $ongoing, $notStarted], // counts
                    'backgroundColor' => [
                        '#4ade80',
                        '#4379eeff',
                        '#dadee6',
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Completed ($completed / $total = {$pCompleted}%)",
                "Ongoing ($ongoing / $total = {$pOngoing}%)",
                "Not Started ($notStarted / $total = {$pNotStarted}%)",
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
                        'padding' => 20, // space between legend items and chart
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
