<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DashboardStats;

class StatsDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $sort = 1;
    protected static ?string $title = 'Dashboard';
    protected static string $routePath = '/';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 1,
            'lg' => 1,
            'xl' => 1,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }

    public function getFooterWidgets(): array
    {
        return [];
    }
}
