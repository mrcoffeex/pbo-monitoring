<?php

namespace App\Filament\Loggers;

use App\Enums\CustomOptions;
use App\Models\Center;
use App\Filament\Resources\CenterResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;
use Spatie\Activitylog\Contracts\Activity;

class CenterLogger extends Logger
{
    public static ?string $model = Center::class;

    public static function getLabel(): string | Htmlable | null
    {
        return CenterResource::getModelLabel();
    }

    public function getSubjectRoute(Activity $activity): ?string
    {
        return CenterResource::getUrl('edit', ['record' => $activity->subject_id]);
    }

    public function getRelationManagerRoute(Activity $activity): ?string
    {
        return $this->getSubjectRoute($activity).'?activeRelationManager=0';
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('code')
                    ->label(__('Code')),

                Field::make('funds')
                    ->label(__('Funds')),

                Field::make('name')
                    ->label(__('Name')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
