<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class PaymentLogger extends Logger
{
    public static ?string $model = Payment::class;

    public static function getLabel(): string|Htmlable|null
    {
        return PaymentResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('Project')),

                Field::make('type')
                    ->label(__('Type of Payment')),

                Field::make('date')
                    ->label(__('Date of Payment')),

                Field::make('amount')
                    ->label(__('Amount'))
                    ->money('PHP'),

                Field::make('implementation.percentage')
                    ->label(__('Implementation')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
