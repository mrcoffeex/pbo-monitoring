<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Project;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyPaymentsLineChart extends ChartWidget
{
    protected static ?string $heading = 'Total Payments per Month';

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

        // Get the 12 months for the selected year
        $months = collect();
        for ($i = 1; $i <= 12; $i++) {
            $months->push(Carbon::createFromDate($selectedYear, $i, 1));
        }

        // Get payment data for each month of the selected year
        $paymentsData = [];
        $labels = [];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Sum all payments within this month, filtered by project year
            $monthlyTotal = Payment::whereBetween('date', [$startOfMonth, $endOfMonth])
                ->whereHas('project', function($query) use ($selectedYear) {
                    $query->where('year', $selectedYear);
                })
                ->sum('amount');

            $paymentsData[] = (float) $monthlyTotal;
            $labels[] = $month->format('M Y');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Payments (PHP)',
                    'data' => $paymentsData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'pointBackgroundColor' => '#3b82f6',
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
        return 2;
    }
}
