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
        // Get the selected filter (year)
        $selectedYear = $this->filter ?? now()->year;

        // Get the 12 months for the selected year
        $months = collect();
        for ($i = 1; $i <= 12; $i++) {
            $months->push(Carbon::createFromDate($selectedYear, $i, 1));
        }

        // Get implementation data for each month of the selected year
        $implementationsData = [];
        $labels = [];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Count implementations for projects that have NTP numbers, filtered by project year
            $monthlyCount = Implementation::whereHas('project.procurements', function ($query) {
                $query->whereNotNull('ntp_number')
                      ->where('ntp_number', '!=', '');
            })
            ->whereHas('project', function($query) use ($selectedYear) {
                $query->where('year', $selectedYear);
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
        return 'line';
    }

    public static function getSort(): int
    {
        return 6;
    }
}
