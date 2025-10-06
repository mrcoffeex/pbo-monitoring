<?php

namespace App\Filament\Pages;

use Noxo\FilamentActivityLog\Pages\ListActivities;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Activities extends ListActivities
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.activities';

    protected bool $isCollapsible = true;

    protected bool $isCollapsed = false;

    public function getTitle(): string
    {
        return "Activity Log";
    }

    public static function getNavigationLabel(): string
    {
        return "Activity Log";
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }

    public function getViewData(): array
    {
        $query = \Spatie\Activitylog\Models\Activity::query()->latest();

        // Get date filters from request
        $startDate = request('start_date')
            ? Carbon::parse(request('start_date'))->startOfDay()
            : now()->subDays(6)->startOfDay();

        $endDate = request('end_date')
            ? Carbon::parse(request('end_date'))->endOfDay()
            : now()->endOfDay();

        // Apply date filter to query
        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by user if not super_admin
        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin')) {
            $query->where('causer_id', $user->id);
        }

        return [
            'activities' => $query->get(),
        ];
    }
}
