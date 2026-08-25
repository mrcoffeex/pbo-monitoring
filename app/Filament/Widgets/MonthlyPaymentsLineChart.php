<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasYearChartFilter;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyPaymentsLineChart extends ChartWidget
{
    use HasYearChartFilter;

    protected static ?string $heading = 'Monthly Payments';

    protected static ?string $description = 'Disbursements by month for the selected project year';

    protected static ?string $maxHeight = '280px';

    public function getColumnSpan(): int|string|array
    {
        return $this->halfWidthSpan();
    }

    protected function getData(): array
    {
        $selectedYear = $this->selectedYear();

        $totals = Payment::query()
            ->whereHas('project', fn ($query) => $query->where('year', $selectedYear))
            ->whereYear('date', $selectedYear)
            ->selectRaw('MONTH(date) as month_number, SUM(amount) as total')
            ->groupBy(DB::raw('MONTH(date)'))
            ->pluck('total', 'month_number')
            ->mapWithKeys(fn ($total, $month): array => [(int) $month => (float) $total]);

        $paymentsData = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $paymentsData[] = round((float) ($totals[$month] ?? 0), 2);
            $labels[] = Carbon::createFromDate($selectedYear, $month, 1)->format('M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Payments',
                    'data' => $paymentsData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.12)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.35,
                    'pointBackgroundColor' => '#3b82f6',
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
        return $this->currencyAxisOptions();
    }

    public static function getSort(): int
    {
        return 3;
    }
}
