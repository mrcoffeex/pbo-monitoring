<?php

namespace App\Filament\Loggers;

use App\Models\Implementation;
use App\Filament\Resources\ImplementationResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class ImplementationLogger extends Logger
{
    public static ?string $model = Implementation::class;

    public static function getLabel(): string | Htmlable | null
    {
        return ImplementationResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('project')),

                Field::make('date')
                    ->label(__('Date')),

                Field::make('percentage')
                    ->label(__('Percentage')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
