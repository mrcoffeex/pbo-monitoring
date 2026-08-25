<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasYearChartFilter;
use App\Models\Implementation;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ImplementationsWithNtpLineChart extends ChartWidget
{
    use HasYearChartFilter;

    protected static ?string $heading = 'Implementations with NTP';

    protected static ?string $description = 'Monthly implementation activity for projects with Notice to Proceed';

    protected static ?string $maxHeight = '280px';

    public function getColumnSpan(): int|string|array
    {
        return $this->fullWidthSpan();
    }

    protected function getData(): array
    {
        $selectedYear = $this->selectedYear();

        $baseQuery = Implementation::query()
            ->whereHas('project.procurements', function (Builder $query): void {
                $query->whereNotNull('ntp_number')
                    ->where('ntp_number', '!=', '');
            })
            ->whereHas('project', fn (Builder $query) => $query->where('year', $selectedYear));

        $byEndDate = (clone $baseQuery)
            ->whereNotNull('end_date')
            ->whereYear('end_date', $selectedYear)
            ->selectRaw('MONTH(end_date) as month_number, COUNT(*) as total')
            ->groupBy(DB::raw('MONTH(end_date)'))
            ->pluck('total', 'month_number')
            ->mapWithKeys(fn ($total, $month): array => [(int) $month => (int) $total]);

        $byDateFallback = (clone $baseQuery)
            ->whereNull('end_date')
            ->whereNotNull('date')
            ->whereYear('date', $selectedYear)
            ->selectRaw('MONTH(date) as month_number, COUNT(*) as total')
            ->groupBy(DB::raw('MONTH(date)'))
            ->pluck('total', 'month_number')
            ->mapWithKeys(fn ($total, $month): array => [(int) $month => (int) $total]);

        $implementationsData = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $implementationsData[] = (int) ($byEndDate[$month] ?? 0) + (int) ($byDateFallback[$month] ?? 0);
            $labels[] = Carbon::createFromDate($selectedYear, $month, 1)->format('M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Implementations',
                    'data' => $implementationsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.12)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#10b981',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointRadius' => 3,
                    'pointHoverRadius' => 5,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array|RawJs|null
    {
        return $this->countAxisOptions();
    }

    public static function getSort(): int
    {
        return 5;
    }
}
