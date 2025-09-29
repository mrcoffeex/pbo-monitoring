<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class DashboardStats extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-stats';

    protected static ?int $sort = 0; // Render first

    public string $year;

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function mount(): void
    {
        $this->year = (string) now()->year;
    }

    public function updatedYear(): void
    {
        Log::info('Dashboard year updated', [
            'new_year' => $this->year,
            'user_id'  => Auth::id(),
        ]);
    }

    public function getAvailableYears(): array
    {
        $years = Project::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn ($y) => (string) $y)
            ->toArray();

        if (empty($years)) {
            $years[] = (string) now()->year;
        }

        $currentYear = (string) now()->year;

        $availableYears = [];

        if (in_array($currentYear, $years)) {
            $availableYears[$currentYear] = $currentYear;
            $years = array_diff($years, [$currentYear]);
        }

        foreach ($years as $year) {
            $availableYears[$year] = $year;
        }

        $availableYears['all'] = 'All Years';

        return $availableYears;
    }

    public function getStatsProperty(): array
    {
        try {
            $selectedYear = $this->year === 'all' ? null : (int) $this->year;

            // Debug log to see when stats are recalculated
            Log::info('Recalculating stats for year: ' . $this->year);

        $paymentStats = $this->getModelGrowthStats(Payment::class, $selectedYear);

        $projectBase = Project::query();
        if ($selectedYear !== null) {
            $projectBase->where('year', $selectedYear);
        }

        $noPurchaseRequestCount    = (clone $projectBase)->doesntHave('purchase_requests')->count();
        $totalProjects             = (clone $projectBase)->count();
        $withPurchaseRequests      = (clone $projectBase)->has('purchase_requests')->count();

        $noPurchaseRequestPercent    = $totalProjects ? round(($noPurchaseRequestCount / $totalProjects) * 100, 1) : 0;
        $withPurchaseRequestsPercent = $totalProjects ? round(($withPurchaseRequests / $totalProjects) * 100, 1) : 0;

        $lastWeekBoundary = now()->copy()->subWeek()->startOfDay();

        $paymentQuery = Payment::query();
        if ($selectedYear !== null) {
            $paymentQuery->whereYear('date', $selectedYear);
        }

        $paymentsTotalAmount    = (float) (clone $paymentQuery)->sum('amount');
        $paymentsLastWeekAmount = (float) (clone $paymentQuery)->where('created_at', '<', $lastWeekBoundary)->sum('amount');
        $paymentsGrowthAmount   = $paymentsTotalAmount - $paymentsLastWeekAmount;
        $paymentsGrowthPercent  = $paymentsLastWeekAmount > 0
            ? round(($paymentsGrowthAmount / $paymentsLastWeekAmount) * 100, 1)
            : 0.0;

        $currency = fn($v) => '₱ ' . number_format($v, 2);

        $onlineWindowMinutes = 10;
        $onlineUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes($onlineWindowMinutes)->getTimestamp())
            ->distinct()
            ->count('user_id');
        $totalUsers    = User::count();
        $onlinePercent = $totalUsers ? round($onlineUsers / $totalUsers * 100, 1) : 0;

        $stats = [
            Stat::make('Users Online', $onlineUsers)
                ->description(new HtmlString("<span class='text-xs'>{$onlinePercent}% of {$totalUsers} users (last {$onlineWindowMinutes}m)</span>"))
                ->icon('heroicon-o-signal')
                ->color($onlineUsers > 0 ? 'success' : 'gray'),

            Stat::make('Projects', Project::when($selectedYear, fn($q) => $q->where('year', $selectedYear))
                ->where('status', 'released')
                ->count())
                ->description('Released Projects')
                ->icon('heroicon-o-folder-open')
                ->color('info'),

            Stat::make('Disbursements', $currency($paymentsTotalAmount))
                ->description("Change vs last week: {$paymentsGrowthPercent}%")
                ->descriptionIcon($paymentsGrowthAmount >= 0 ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down')
                ->color($paymentsGrowthAmount >= 0 ? 'success' : 'primary')
                ->icon('heroicon-o-currency-dollar')
                ->chart(array_values($paymentStats['daily_counts'])),

            Stat::make('Procurements', Procurement::when(
                $selectedYear,
                fn($q) => $q->whereHas('project', fn($p) => $p->where('year', $selectedYear))
            )->count())
                ->description('On-Going Procurements')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Issued NOA', Procurement::whereNotNull('noa_date_received')
                ->when($selectedYear, fn($q) => $q->whereHas('project', fn($p) => $p->where('year', $selectedYear)))->count())
                ->description('Projects with NOA')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Issued NTP', Procurement::whereNotNull('ntp_number')
                ->when($selectedYear, fn($q) => $q->whereHas('project', fn($p) => $p->where('year', $selectedYear)))->count())
                ->description('Projects with NTP')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('With Purchase Requests', $withPurchaseRequests)
                ->description(new HtmlString("
                    <div class='space-y-1 text-xs'>
                        <div>{$withPurchaseRequestsPercent}% with PR of {$totalProjects}</div>
                        <div class='h-2 w-full rounded bg-gray-200 dark:bg-gray-800'>
                            <div class='h-2 rounded bg-blue-500' style='width: {$withPurchaseRequestsPercent}%'></div>
                        </div>
                    </div>
                "))
                ->icon('heroicon-o-check')
                ->color('info'),

            Stat::make('No Purchase Request', $noPurchaseRequestCount)
                ->description(new HtmlString("
                    <div class='space-y-1 text-xs'>
                        <div>{$noPurchaseRequestPercent}% without PR of {$totalProjects}</div>
                        <div class='h-2 w-full rounded bg-gray-200 dark:bg-gray-800'>
                            <div class='h-2 rounded bg-red-500' style='width: {$noPurchaseRequestPercent}%'></div>
                        </div>
                    </div>
                "))
                ->icon('heroicon-o-x-mark')
                ->color('danger'),
        ];

        return $stats;
        } catch (\Exception $e) {
            Log::error('Error calculating dashboard stats', [
                'error' => $e->getMessage(),
                'year' => $this->year
            ]);

            return [
                Stat::make('Error', 'Failed to load stats')
                    ->description('Please try refreshing the page')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }

    private function getModelGrowthStats(string $modelClass, ?int $year, string $dateColumn = 'created_at'): array
    {
        $now      = now();
        $lastWeek = $now->copy()->subWeek()->startOfDay();

        $total = $modelClass::when($year, fn($q) => $q->whereYear($dateColumn, $year))->count();

        $lastWeekCount = $modelClass::when($year, fn($q) => $q->whereYear($dateColumn, $year))
            ->where($dateColumn, '<', $lastWeek)
            ->count();

        $growth        = $total - $lastWeekCount;
        $growthPercent = $lastWeekCount > 0 ? round(($growth / $lastWeekCount) * 100, 1) : 0;

        $dailyRaw = $modelClass::when($year, fn($q) => $q->whereYear($dateColumn, $year))
            ->selectRaw("DATE($dateColumn) as d, COUNT(*) as c")
            ->whereDate($dateColumn, '>=', $now->copy()->subDays(6)->toDateString())
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->toDateString();
            $daily[$day] = $dailyRaw[$day] ?? 0;
        }

        return [
            'total'          => $total,
            'last_week'      => $lastWeekCount,
            'growth'         => $growth,
            'growth_percent' => $growthPercent,
            'daily_counts'   => $daily,
        ];
    }
}
