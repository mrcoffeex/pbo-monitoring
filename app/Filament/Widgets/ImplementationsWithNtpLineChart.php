<?php

namespace App\Filament\Widgets;

use App\Models\Implementation;
use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ImplementationsWithNtpLineChart extends ChartWidget
{
    protected static ?string $heading = 'Implementations per Month (Projects with NTP)';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Get the last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        // Get implementation data for the last 12 months
        $implementationsData = [];
        $labels = [];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Count implementations for projects that have NTP numbers
            $monthlyCount = Implementation::whereHas('project.procurements', function ($query) {
                $query->whereNotNull('ntp_number')
                      ->where('ntp_number', '!=', '');
            })
            ->whereBetween('end_date', [$startOfMonth, $endOfMonth])
            ->count();

            $implementationsData[] = $monthlyCount;
            $labels[] = $month->format('M Y');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Implementations',
                    'data' => $implementationsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function getSort(): int
    {
        return 6;
    }
}
