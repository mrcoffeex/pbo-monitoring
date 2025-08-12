<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Implementation;
use Filament\Widgets\ChartWidget;

class ImplementationChart extends ChartWidget
{
    protected static ?string $heading = 'Implementations per Day';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $startDate = now()->subDays(15)->startOfDay();
        $endDate = now()->endOfDay();

        $implementations = Implementation::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $labels = [];
        $data = [];

        foreach (range(15, 0) as $i) {
            $day = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($day)->toDateString();

            $count = $implementations->get($day, collect())->count();
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Implementations',
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
