<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class StatsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard-stats';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 1;

    public string $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = (string) now()->year;
    }

    public function updatedSelectedYear(): void
    {
        // Livewire will automatically re-render
    }

    public function getAvailableYears(): array
    {
        $years = Project::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($y) => (string) $y)
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

    public function getStatsData(): array
    {
        $selectedYear = $this->selectedYear === 'all' ? null : (int) $this->selectedYear;

        // Projects stats
        $projectBase = Project::query();
        if ($selectedYear !== null) {
            $projectBase->where('year', $selectedYear);
        }

        $releasedProjects = (clone $projectBase)->where('status', 'released')->count();
        $totalProjects = (clone $projectBase)->count();
        $noPurchaseRequestCount = (clone $projectBase)->doesntHave('purchase_requests')->count();
        $withPurchaseRequests = (clone $projectBase)->has('purchase_requests')->count();

        $noPurchaseRequestPercent = $totalProjects ? round(($noPurchaseRequestCount / $totalProjects) * 100, 1) : 0;
        $withPurchaseRequestsPercent = $totalProjects ? round(($withPurchaseRequests / $totalProjects) * 100, 1) : 0;

        // Payment stats
        $paymentQuery = Payment::query();
        if ($selectedYear !== null) {
            $paymentQuery->whereYear('created_at', $selectedYear);
        }

        $lastWeekBoundary = now()->copy()->subWeek()->startOfDay();
        $paymentsTotalAmount = (float) (clone $paymentQuery)->sum('amount');
        $paymentsLastWeekAmount = (float) (clone $paymentQuery)->where('created_at', '<', $lastWeekBoundary)->sum('amount');
        $paymentsGrowthAmount = $paymentsTotalAmount - $paymentsLastWeekAmount;
        $paymentsGrowthPercent = $paymentsLastWeekAmount > 0
            ? round(($paymentsGrowthAmount / $paymentsLastWeekAmount) * 100, 1)
            : 0.0;

        // Payment chart data (last 7 days)
        $paymentChartData = $this->getPaymentChartData($selectedYear);

        // Procurement stats
        $procurementQuery = Procurement::query();
        if ($selectedYear !== null) {
            $procurementQuery->whereHas('project', fn($q) => $q->where('year', $selectedYear));
        }

        $totalProcurements = (clone $procurementQuery)->count();
        $procurementsWithNOA = (clone $procurementQuery)->whereNotNull('noa_date_received')->count();
        $procurementsWithNTP = (clone $procurementQuery)->whereNotNull('ntp_number')->count();

        // Users online
        $onlineWindowMinutes = 10;
        $onlineUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes($onlineWindowMinutes)->getTimestamp())
            ->distinct()
            ->count('user_id');
        $totalUsers = User::count();
        $onlinePercent = $totalUsers ? round($onlineUsers / $totalUsers * 100, 1) : 0;

        return [
            'onlineUsers' => $onlineUsers,
            'onlinePercent' => $onlinePercent,
            'totalUsers' => $totalUsers,
            'onlineWindowMinutes' => $onlineWindowMinutes,

            'releasedProjects' => $releasedProjects,
            'totalProjects' => $totalProjects,

            'paymentsTotalAmount' => $paymentsTotalAmount,
            'paymentsGrowthPercent' => $paymentsGrowthPercent,
            'paymentsGrowthAmount' => $paymentsGrowthAmount,
            'paymentChartData' => $paymentChartData,

            'totalProcurements' => $totalProcurements,
            'procurementsWithNOA' => $procurementsWithNOA,
            'procurementsWithNTP' => $procurementsWithNTP,

            'withPurchaseRequests' => $withPurchaseRequests,
            'withPurchaseRequestsPercent' => $withPurchaseRequestsPercent,
            'noPurchaseRequestCount' => $noPurchaseRequestCount,
            'noPurchaseRequestPercent' => $noPurchaseRequestPercent,
        ];
    }

    private function getPaymentChartData(?int $year): array
    {
        $now = now();
        $dailyRaw = Payment::query()
            ->when($year, fn($q) => $q->whereYear('created_at', $year))
            ->selectRaw("DATE(created_at) as d, COUNT(*) as c")
            ->whereDate('created_at', '>=', $now->copy()->subDays(6)->toDateString())
            ->groupBy('d')
            ->pluck('c', 'd')
            ->toArray();

        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->toDateString();
            $daily[] = $dailyRaw[$day] ?? 0;
        }

        return $daily;
    }
}
