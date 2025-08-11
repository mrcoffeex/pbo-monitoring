<?php

namespace App\Filament\Loggers;

use App\Models\PurchaseRequestControl;
use App\Filament\Resources\PurchaseRequestControlResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class PurchaseRequestControlLogger extends Logger
{
    public static ?string $model = PurchaseRequestControl::class;

    public static function getLabel(): string | Htmlable | null
    {
        return PurchaseRequestControlResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('Project')),

                Field::make('controlled_date')
                    ->label(__('Controlled Date')),

                Field::make('control_number')
                    ->label(__('Control Number')),

                Field::make('amount')
                    ->label(__('Amount'))
                    ->money('PHP'),
            ])
            ->relationManagers([
                //
            ]);
    }
}
