<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Js;

class FinancialStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Status (Obligated vs Not Obligated)';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $projects = Project::with([
            'obligation_requests:id,project_id,amount',
        ])->get();

        $obligated = 0;
        $notObligated = 0;

        $projects->each(function ($project) use (&$obligated, &$notObligated) {
            $totalObligated = (float) $project->obligation_requests->sum('amount');
            if ($totalObligated > 0) {
                $obligated++;
            } else {
                $notObligated++;
            }
        });

        $total = max($obligated + $notObligated, 1);

        $pObligated     = round(($obligated / $total) * 100, 1);
        $pNotObligated  = round(($notObligated / $total) * 100, 1);

        return [
            'datasets' => [
                [
                    'label' => 'Projects (%)',
                    'data' => [$pObligated, $pNotObligated],         // percentages as dataset values
                    'rawCounts' => [$obligated, $notObligated],      // raw counts for tooltips
                    'backgroundColor' => [
                        '#1ec55c', // obligated
                        '#dadee6', // not obligated
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Obligated ($obligated / $total = {$pObligated}%)",
                "Not Obligated ($notObligated / $total = {$pNotObligated}%)",
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
}
