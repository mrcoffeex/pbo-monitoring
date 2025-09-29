<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\FinancialOverviewBarChart;
use App\Filament\Widgets\FinancialStatusPieChart;
use App\Filament\Widgets\ImplementationsWithNtpLineChart;
use App\Filament\Widgets\MonthlyPaymentsLineChart;
use App\Filament\Widgets\ProjectStatusPieChart;
use Filament\Pages\Dashboard as BaseDashboard;

class ChartDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Statistics';
    protected static ?int $sort = null;
    protected static ?string $title = 'Statistics Overview';
    protected static string $routePath = 'stats';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 4,
            'xl' => 6,
        ];
    }

    public function getWidgets(): array
    {
        return [
            FinancialOverviewBarChart::class,
            FinancialStatusPieChart::class,
            ImplementationsWithNtpLineChart::class,
            MonthlyPaymentsLineChart::class,
            ProjectStatusPieChart::class,
        ];
    }
}
