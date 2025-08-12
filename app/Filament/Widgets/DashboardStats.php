<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\ImplementationChart;
use App\Filament\Widgets\PaymentChart;
use App\Models\User;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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

        return [
            // Stat::make('Users', number_format($userStats['total']))
            //     ->description("Up by {$userStats['growth_percent']}% vs last week")
            //     ->descriptionIcon($userStats['growth'] >= 0 ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down')
            //     ->color($userStats['growth'] >= 0 ? 'primary' : 'danger')
            //     ->icon('heroicon-o-user-group')
            //     ->chartColor($userStats['growth'] >= 0 ? 'primary' : 'danger')
            //     ->chart(array_values($userStats['daily_counts']))
            //     ->extraAttributes([
            //         'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
            //     ]),

            Stat::make('Projects', Project::where([
                    'year' => now()->format('Y'),
                    'status' => 'approved',
                ])->count())
                ->label('Projects')
                ->description('Approved Projects')
                ->icon('heroicon-o-folder-open')
                ->color('info'),

            Stat::make('Procurements', Procurement::count())
                ->label('Procurements')
                ->description('Projects with On-Going Procurements')
                ->icon('heroicon-o-shopping-cart')
                ->color('primary'),

            Stat::make('Payments', number_format($paymentStats['total']))
                ->description("Up by {$paymentStats['growth_percent']}% vs last week")
                ->descriptionIcon($paymentStats['growth'] >= 0 ? 'heroicon-o-chevron-up' : 'heroicon-o-chevron-down')
                ->color($paymentStats['growth'] >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar')
                ->chartColor($paymentStats['growth'] >= 0 ? 'success' : 'danger')
                ->chart(array_values($paymentStats['daily_counts']))
                ->extraAttributes([
                    'class' => 'shadow-md ring-1 ring-offset-1 ring-primary-100 transition-all duration-300 hover:scale-[1.02]',
                ]),

            Stat::make('Issued NOA', Procurement::whereNotNull('noa_date_received')->count())
                ->description('Projects with Issued NOA')
                ->icon('heroicon-o-document-text')
                ->color('success'),

            Stat::make('Issued NTP', Procurement::whereNotNull('ntp_number')->count())
                ->description('Projects with Issued NTP')
                ->icon('heroicon-o-document-text')
                ->color('success'),

            Stat::make('No Purchase Request', $noPurchaseRequestCount)
                ->description("Projects without Purchase Request - {$noPurchaseRequestPercent}%")
                ->icon('heroicon-o-clipboard-document')
                ->color('danger'),
        ];
    }


    public static function getCharts(): array
    {
        return [
            ImplementationChart::class,
            PaymentChart::class,
        ];
    }

}
