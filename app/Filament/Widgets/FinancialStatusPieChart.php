<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class FinancialStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Status (Obligated vs Not Obligated)';

    protected function getData(): array
    {
        $projects = Project::with([
            'obligation_requests:id,project_id,amount',
        ])->get();

        $obligated = 0;
        $notObligated = 0;

        $projects->each(function ($project) use (&$obligated, &$notObligated) {
            $totalObligated = $project->obligation_requests->sum('amount');
            if ($totalObligated > 0) {
                $obligated++;
            } else {
                $notObligated++;
            }
        });

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [$obligated, $notObligated],
                    'backgroundColor' => [
                        '#1ec55cff', // obligated
                        '#dadee6ff', // not obligated
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Obligated ($obligated)",
                "Not Obligated ($notObligated)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
