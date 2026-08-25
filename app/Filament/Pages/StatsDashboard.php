<?php

namespace App\Filament\Pages;

use App\Enums\CustomOptions;
use App\Models\Implementation;
use App\Models\ObligationRequest;
use App\Models\Payment;
use App\Models\Procurement;
use App\Models\Project;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class StatsDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard-stats';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static string $routePath = '/';

    public string $selectedYear;

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [];
    }

    public function mount(): void
    {
        $this->selectedYear = (string) now()->year;
    }

    public function updatedSelectedYear(): void
    {
        $this->dispatch('dashboard-charts-updated', charts: $this->dashboard['charts']);
    }

    /**
     * @return array<string, string>
     */
    public function getAvailableYears(): array
    {
        $years = Project::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn ($year): string => (string) $year)
            ->all();

        if ($years === []) {
            $years[] = (string) now()->year;
        }

        $currentYear = (string) now()->year;
        $availableYears = [];

        if (in_array($currentYear, $years, true)) {
            $availableYears[$currentYear] = $currentYear;
            $years = array_values(array_diff($years, [$currentYear]));
        }

        foreach ($years as $year) {
            $availableYears[$year] = $year;
        }

        $availableYears['all'] = 'All Years';

        return $availableYears;
    }

    /**
     * @return array{cards: list<array<string, mixed>>, charts: array<string, mixed>}
     */
    #[Computed]
    public function dashboard(): array
    {
        return $this->buildDashboardData();
    }

    /**
     * @return array{cards: list<array<string, mixed>>, charts: array<string, mixed>}
     */
    public function getDashboardData(): array
    {
        return $this->dashboard;
    }

    /**
     * @return array{cards: list<array<string, mixed>>, charts: array<string, mixed>}
     */
    private function buildDashboardData(): array
    {
        $year = $this->selectedYearFilter();

        $projectQuery = Project::query()->when(
            $year !== null,
            fn (Builder $query): Builder => $query->where('year', $year),
        );

        $totalProjects = (clone $projectQuery)->count();
        $releasedProjects = (clone $projectQuery)->where('status', 'released')->count();
        $unreleasedProjects = (clone $projectQuery)->where('status', 'unreleased')->count();
        $canceledProjects = (clone $projectQuery)->where('status', 'canceled')->count();
        $withPurchaseRequests = (clone $projectQuery)->has('purchase_requests')->count();
        $totalAppropriation = (float) (clone $projectQuery)->sum('appropriation');
        $totalAllotment = (float) (clone $projectQuery)->sum('allotment');

        $paymentQuery = $this->relatedToProjectYear(Payment::query(), $year);
        $paymentsTotalAmount = (float) (clone $paymentQuery)->sum('amount');

        $lastWeekBoundary = now()->copy()->subWeek()->startOfDay();
        $paymentsLastWeekAmount = (float) (clone $paymentQuery)->where('created_at', '<', $lastWeekBoundary)->sum('amount');
        $paymentsGrowthAmount = $paymentsTotalAmount - $paymentsLastWeekAmount;
        $paymentsGrowthPercent = $paymentsLastWeekAmount > 0
            ? round(($paymentsGrowthAmount / $paymentsLastWeekAmount) * 100, 1)
            : 0.0;

        $totalObligated = (float) $this->relatedToProjectYear(ObligationRequest::query(), $year)->sum('amount');

        $procurementQuery = $this->relatedToProjectYear(Procurement::query(), $year);
        $totalProcurements = (clone $procurementQuery)->count();
        $procurementsWithNOA = (clone $procurementQuery)->whereNotNull('noa_date_received')->count();
        $procurementsWithNTP = (clone $procurementQuery)
            ->whereNotNull('ntp_number')
            ->where('ntp_number', '!=', '')
            ->count();

        $onlineWindowMinutes = 10;
        $onlineUsers = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes($onlineWindowMinutes)->getTimestamp())
            ->distinct()
            ->count('user_id');
        $totalUsers = User::query()->count();
        $onlinePercent = $totalUsers ? round($onlineUsers / $totalUsers * 100, 1) : 0.0;

        $prCoveragePercent = $totalProjects
            ? round(($withPurchaseRequests / $totalProjects) * 100, 1)
            : 0.0;

        $months = $this->monthSeries($year);

        return [
            'cards' => $this->statCards([
                'onlineUsers' => $onlineUsers,
                'onlinePercent' => $onlinePercent,
                'totalUsers' => $totalUsers,
                'releasedProjects' => $releasedProjects,
                'totalProjects' => $totalProjects,
                'totalAppropriation' => $totalAppropriation,
                'totalObligated' => $totalObligated,
                'paymentsTotalAmount' => $paymentsTotalAmount,
                'paymentsGrowthPercent' => $paymentsGrowthPercent,
                'totalProcurements' => $totalProcurements,
                'procurementsWithNOA' => $procurementsWithNOA,
                'procurementsWithNTP' => $procurementsWithNTP,
                'withPurchaseRequests' => $withPurchaseRequests,
                'prCoveragePercent' => $prCoveragePercent,
            ]),
            'charts' => [
                'financialOverview' => [
                    ['name' => 'Appropriation', 'amount' => round($totalAppropriation, 2)],
                    ['name' => 'Allotment', 'amount' => round($totalAllotment, 2)],
                    ['name' => 'Obligated', 'amount' => round($totalObligated, 2)],
                    ['name' => 'Disbursed', 'amount' => round($paymentsTotalAmount, 2)],
                ],
                'monthlyPayments' => $this->monthlyPaymentSeries($paymentQuery, $months),
                'projectStatus' => [
                    ['name' => 'Released', 'value' => $releasedProjects],
                    ['name' => 'Unreleased', 'value' => $unreleasedProjects],
                    ['name' => 'Canceled', 'value' => $canceledProjects],
                ],
                'procurementPipeline' => [
                    ['name' => 'Projects', 'value' => $totalProjects],
                    ['name' => 'With PR', 'value' => $withPurchaseRequests],
                    ['name' => 'Procurements', 'value' => $totalProcurements],
                    ['name' => 'With NOA', 'value' => $procurementsWithNOA],
                    ['name' => 'With NTP', 'value' => $procurementsWithNTP],
                ],
                'paymentTypes' => $this->paymentTypeSeries($paymentQuery),
                'projectTypes' => $this->projectTypeSeries($projectQuery),
                'monthlyImplementations' => $this->monthlyImplementationSeries($year, $months),
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $stats
     * @return list<array<string, mixed>>
     */
    private function statCards(array $stats): array
    {
        return [
            [
                'label' => 'Online Users',
                'value' => number_format((int) $stats['onlineUsers']),
                'hint' => $stats['onlinePercent'].'% of '.number_format((int) $stats['totalUsers']),
                'icon' => 'users',
                'tone' => 'emerald',
            ],
            [
                'label' => 'Released Projects',
                'value' => number_format((int) $stats['releasedProjects']),
                'hint' => 'of '.number_format((int) $stats['totalProjects']).' total',
                'icon' => 'check',
                'tone' => 'blue',
            ],
            [
                'label' => 'Appropriation',
                'value' => $this->compactPhp((float) $stats['totalAppropriation']),
                'hint' => '₱'.number_format((float) $stats['totalAppropriation'], 2),
                'icon' => 'bank',
                'tone' => 'indigo',
            ],
            [
                'label' => 'Obligated',
                'value' => $this->compactPhp((float) $stats['totalObligated']),
                'hint' => '₱'.number_format((float) $stats['totalObligated'], 2),
                'icon' => 'document',
                'tone' => 'purple',
            ],
            [
                'label' => 'Disbursed',
                'value' => $this->compactPhp((float) $stats['paymentsTotalAmount']),
                'hint' => ($stats['paymentsGrowthPercent'] >= 0 ? '+' : '').$stats['paymentsGrowthPercent'].'% vs last week',
                'hintTone' => $stats['paymentsGrowthPercent'] >= 0 ? 'positive' : 'negative',
                'icon' => 'currency',
                'tone' => 'amber',
            ],
            [
                'label' => 'Procurements',
                'value' => number_format((int) $stats['totalProcurements']),
                'hint' => number_format((int) $stats['procurementsWithNOA']).' with NOA',
                'icon' => 'clipboard',
                'tone' => 'fuchsia',
            ],
            [
                'label' => 'With NTP',
                'value' => number_format((int) $stats['procurementsWithNTP']),
                'hint' => 'Notice to Proceed issued',
                'icon' => 'badge',
                'tone' => 'cyan',
            ],
            [
                'label' => 'PR Coverage',
                'value' => $stats['prCoveragePercent'].'%',
                'hint' => number_format((int) $stats['withPurchaseRequests']).' projects with PR',
                'icon' => 'queue',
                'tone' => 'green',
            ],
        ];
    }

    /**
     * @param  list<array{key: string, label: string, start: string, end: string}>  $months
     * @return list<array{month: string, amount: float}>
     */
    private function monthlyPaymentSeries(Builder $paymentQuery, array $months): array
    {
        $start = $months[0]['start'] ?? now()->startOfYear()->toDateString();
        $end = $months[array_key_last($months)]['end'] ?? now()->endOfYear()->toDateString();

        $totals = (clone $paymentQuery)
            ->whereBetween('date', [$start, $end])
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, SUM(amount) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        return collect($months)
            ->map(fn (array $month): array => [
                'month' => $month['label'],
                'amount' => round((float) ($totals[$month['key']] ?? 0), 2),
            ])
            ->all();
    }

    /**
     * @return list<array{name: string, value: float}>
     */
    private function paymentTypeSeries(Builder $paymentQuery): array
    {
        $totals = (clone $paymentQuery)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return $totals
            ->map(fn ($total, $type): array => [
                'name' => CustomOptions::PAYMENTS[$type] ?? (string) $type,
                'value' => round((float) $total, 2),
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, value: int}>
     */
    private function projectTypeSeries(Builder $projectQuery): array
    {
        $counts = [];

        (clone $projectQuery)->get(['type'])->each(function (Project $project) use (&$counts): void {
            foreach ((array) $project->type as $type) {
                $label = CustomOptions::PROJECT_TYPES[$type] ?? (string) $type;
                $counts[$label] = ($counts[$label] ?? 0) + 1;
            }
        });

        arsort($counts);

        return collect($counts)
            ->map(fn (int $value, string $name): array => [
                'name' => $name,
                'value' => $value,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  list<array{key: string, label: string, start: string, end: string}>  $months
     * @return list<array{month: string, count: int}>
     */
    private function monthlyImplementationSeries(?int $year, array $months): array
    {
        $start = $months[0]['start'] ?? now()->startOfYear()->toDateString();
        $end = $months[array_key_last($months)]['end'] ?? now()->endOfYear()->toDateString();

        $implementations = $this->relatedToProjectYear(Implementation::query(), $year)
            ->whereHas('project.procurements', function (Builder $query): void {
                $query->whereNotNull('ntp_number')
                    ->where('ntp_number', '!=', '');
            })
            ->where(function (Builder $query) use ($start, $end): void {
                $query->whereBetween('end_date', [$start, $end])
                    ->orWhere(function (Builder $nested) use ($start, $end): void {
                        $nested->whereNull('end_date')
                            ->whereBetween('date', [$start, $end]);
                    });
            })
            ->get(['end_date', 'date']);

        $totals = $implementations
            ->map(fn (Implementation $implementation): ?string => filled($implementation->end_date ?? $implementation->date)
                ? Carbon::parse($implementation->end_date ?? $implementation->date)->format('Y-m')
                : null)
            ->filter()
            ->countBy()
            ->all();

        return collect($months)
            ->map(fn (array $month): array => [
                'month' => $month['label'],
                'count' => (int) ($totals[$month['key']] ?? 0),
            ])
            ->all();
    }

    /**
     * @return list<array{key: string, label: string, start: string, end: string}>
     */
    private function monthSeries(?int $year): array
    {
        if ($year === null) {
            return collect(range(11, 0))
                ->map(function (int $offset): array {
                    $date = now()->copy()->subMonths($offset)->startOfMonth();

                    return [
                        'key' => $date->format('Y-m'),
                        'label' => $date->format('M Y'),
                        'start' => $date->toDateString(),
                        'end' => $date->copy()->endOfMonth()->toDateString(),
                    ];
                })
                ->all();
        }

        return collect(range(1, 12))
            ->map(function (int $month) use ($year): array {
                $date = Carbon::createFromDate($year, $month, 1)->startOfMonth();

                return [
                    'key' => $date->format('Y-m'),
                    'label' => $date->format('M'),
                    'start' => $date->toDateString(),
                    'end' => $date->copy()->endOfMonth()->toDateString(),
                ];
            })
            ->all();
    }

    private function relatedToProjectYear(Builder $query, ?int $year): Builder
    {
        if ($year === null) {
            return $query;
        }

        return $query->whereHas(
            'project',
            fn (Builder $projectQuery): Builder => $projectQuery->where('year', $year),
        );
    }

    private function selectedYearFilter(): ?int
    {
        return $this->selectedYear === 'all' ? null : (int) $this->selectedYear;
    }

    private function compactPhp(float $amount): string
    {
        $absolute = abs($amount);

        if ($absolute >= 1_000_000_000) {
            return '₱'.number_format($amount / 1_000_000_000, 2).'B';
        }

        if ($absolute >= 1_000_000) {
            return '₱'.number_format($amount / 1_000_000, 2).'M';
        }

        if ($absolute >= 1_000) {
            return '₱'.number_format($amount / 1_000, 1).'K';
        }

        return '₱'.number_format($amount, 2);
    }
}
