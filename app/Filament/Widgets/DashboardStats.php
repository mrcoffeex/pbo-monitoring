<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class DashboardStats extends BaseWidget
{

    protected function getStats(): array
    {
        function getModelGrowthStats(string $modelClass, string $dateColumn = 'created_at'): array
        {
            $now = now();
            $lastWeek = $now->copy()->subWeek()->startOfDay();

            $total = $modelClass::count();
            $lastWeekCount = $modelClass::where($dateColumn, '<', $lastWeek)->count();
            $growth = $total - $lastWeekCount;
            $growthPercent = $lastWeekCount > 0 ? round(($growth / $lastWeekCount) * 100, 1) : 0;

            $dailyCountsRaw = $modelClass::query()
                ->selectRaw("DATE($dateColumn) as date, COUNT(*) as count")
                ->whereDate($dateColumn, '>=', $now->copy()->subDays(6)->startOfDay())
                ->groupByRaw("DATE($dateColumn)")
                ->orderByRaw("DATE($dateColumn)")
                ->pluck('count', 'date')
                ->toArray();

            $dailyCounts = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i)->toDateString();
                $dailyCounts[$date] = $dailyCountsRaw[$date] ?? 0;
            }

            return [
                'total' => $total,
                'last_week' => $lastWeekCount,
                'growth' => $growth,
                'growth_percent' => $growthPercent,
                'daily_counts' => $dailyCounts,
            ];
        }

        $userStats = getModelGrowthStats(User::class);
        $paymentStats = getModelGrowthStats(Payment::class);

        $noPurchaseRequestCount = Project::doesntHave('purchase_requests')->count();
        $totalProjects = Project::count();
        $noPurchaseRequestPercent = $totalProjects > 0 ? round(($noPurchaseRequestCount / $totalProjects) * 100, 1) : 0;

        $withPurchaseRequests = Project::has('purchase_requests')->count();
        $withPurchaseRequestsPercent = $totalProjects > 0 ? round(($withPurchaseRequests / $totalProjects) * 100, 1) : 0;

        $lastWeekBoundary = now()->copy()->subWeek()->startOfDay();
        $paymentsTotalAmount = (float) Payment::sum('amount');
        $paymentsLastWeekAmount = (float) Payment::where('created_at', '<', $lastWeekBoundary)->sum('amount');
        $paymentsGrowthAmount = $paymentsTotalAmount - $paymentsLastWeekAmount;
        $paymentsGrowthPercent = $paymentsLastWeekAmount > 0
            ? round(($paymentsGrowthAmount / $paymentsLastWeekAmount) * 100, 1)
            : 0.0;

        $currency = fn($v) => '₱ ' . number_format($v, 2);

        // Online users (active within last 10 minutes via sessions table)
        $onlineWindowMinutes = 10;
        $onlineUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes($onlineWindowMinutes)->getTimestamp())
            ->distinct()
            ->count('user_id');
        $totalUsers = User::count();
        $onlinePercent = $totalUsers > 0 ? round(($onlineUsers / max($totalUsers,1)) * 100, 1) : 0;

        return [
            Stat::make('Users Online', $onlineUsers)
                ->description(new HtmlString("<span class='text-xs'>{$onlinePercent}% of {$totalUsers} users active (last {$onlineWindowMinutes}m)</span>"))
                ->icon('heroicon-o-signal')
                ->color($onlineUsers > 0 ? 'success' : 'gray')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),
            Stat::make('Projects', Project::where([
                    'year' => now()->format('Y'),
                    'status' => 'approved',
                ])->count())
                ->label('Projects')
                ->description('Approved Projects')
                ->icon('heroicon-o-folder-open')
                ->color('info')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('Disbursements', $currency($paymentsTotalAmount))
                ->description("Up by {$paymentsGrowthPercent}% vs last week")
                ->descriptionIcon($paymentsGrowthAmount >= 0 ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down')
                ->color($paymentsGrowthAmount >= 0 ? 'success' : 'primary')
                ->icon('heroicon-o-currency-dollar')
                ->chartColor($paymentsGrowthAmount >= 0 ? 'success' : 'primary')
                ->chart(array_values($paymentStats['daily_counts']))
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('Procurements', Procurement::count())
                ->label('Procurements')
                ->description('Projects with On-Going Procurements')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('Issued NOA', Procurement::whereNotNull('noa_date_received')->count())
                ->description('Projects with Issued NOA')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('Issued NTP', Procurement::whereNotNull('ntp_number')->count())
                ->description('Projects with Issued NTP')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('With Purchase Requests', $withPurchaseRequests)
                ->description(new HtmlString("
                    <div class='space-y-1 text-xs'>
                        <div class='flex justify-between'>
                            <span>{$withPurchaseRequestsPercent}% with purchase requests out of {$totalProjects} of Registered Projects</span>
                        </div>
                        <div class='h-2 w-full rounded bg-gray-200/70 dark:bg-gray-800'>
                            <div class='h-2 rounded bg-blue-500' style='width: {$withPurchaseRequestsPercent}%;'></div>
                        </div>
                    </div>
                "))
                ->icon('heroicon-o-check')
                ->color('info')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('No Purchase Request', $noPurchaseRequestCount)
                ->description(new HtmlString("
                    <div class='space-y-1 text-xs'>
                        <div class='flex justify-between'>
                            <span>{$noPurchaseRequestPercent}% without purchase requests out of {$totalProjects} of Registered Projects</span>
                        </div>
                        <div class='h-2 w-full rounded bg-gray-200/70 dark:bg-gray-800'>
                            <div class='h-2 rounded bg-red-500' style='width: {$noPurchaseRequestPercent}%;'></div>
                        </div>
                    </div>
                "))
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),
        ];
    }
}
