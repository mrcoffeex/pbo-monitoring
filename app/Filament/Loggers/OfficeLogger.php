<?php

namespace App\Filament\Loggers;

use App\Models\Office;
use App\Filament\Resources\OfficeResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class OfficeLogger extends Logger
{
    public static ?string $model = Office::class;

    public static function getLabel(): string | Htmlable | null
    {
        return OfficeResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('name')
                    ->label(__('Office Name')),

                Field::make('emails')
                    ->label(__('Email Addresses')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
