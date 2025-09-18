<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyPaymentsLineChart extends ChartWidget
{
    protected static ?string $heading = 'Total Payments per Month (Last 12 Months)';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Get the last 12 months
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        // Get payment data for the last 12 months
        $paymentsData = [];
        $labels = [];

        foreach ($months as $month) {
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();

            // Sum all payments within this month
            $monthlyTotal = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
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

    protected function getType(): string
    {
        return 'line';
    }

    public static function getSort(): int
    {
        return 2;
    }
}
