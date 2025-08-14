<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Payments per Day';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startDate = now()->subDays(15)->startOfDay();
        $endDate = now()->endOfDay();

        $payments = Payment::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $labels = [];
        $data = [];

        foreach (range(15, 0) as $i) {
            $day = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($day)->format('Md Y');

            $count = $payments->get($day, collect())->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data' => $data,
                    'borderColor' => '#ff3ea8ff',
                    'backgroundColor' => '#ff71bfff',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
