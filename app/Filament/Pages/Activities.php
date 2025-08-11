<?php

namespace App\Filament\Pages;

use Noxo\FilamentActivityLog\Pages\ListActivities;
use Spatie\Activitylog\Contracts\Activity;
use Illuminate\Support\Facades\Auth;

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
        return 'System';
    }

    public static function getNavigationSort(): int
    {
        return 3;
    }
    
    public function getViewData(): array
    {
        return [
            'activities' => \Spatie\Activitylog\Models\Activity::latest()->limit(20)->get(),
        ];
    }
}
