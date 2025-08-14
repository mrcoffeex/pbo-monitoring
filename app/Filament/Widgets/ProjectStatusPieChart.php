<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Collection;

class ProjectStatusPieChart extends ChartWidget
{
    protected static ?string $heading = 'Project Status Distribution';

    protected function getData(): array
    {
        // Load needed relations once
        $projects = Project::with([
            'purchase_requests:id,project_id',
            'technical_working_groups:id,project_id,abc',
            'payments:id,project_id,amount',
        ])->get();

        $completed = 0;
        $ongoing = 0;
        $notStarted = 0;

        $projects->each(function ($project) use (&$completed, &$ongoing, &$notStarted) {
            $hasPR = $project->purchase_requests->isNotEmpty();

            $abcTotal = $project->technical_working_groups->sum('abc');
            $paidTotal = $project->payments->sum('amount');

            $isCompleted = $abcTotal > 0 && $paidTotal >= $abcTotal;

            if ($isCompleted) {
                $completed++;
                return;
            }

            if ($hasPR) {
                $ongoing++;
                return;
            }

            // No purchase requests and not completed
            $notStarted++;
        });

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [$completed, $ongoing, $notStarted],
                    'backgroundColor' => [
                        '#16a34a', // completed
                        '#2563eb', // ongoing
                        '#dadee6ff', // not started
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [
                "Completed ($completed)",
                "Ongoing ($ongoing)",
                "Not Started ($notStarted)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
