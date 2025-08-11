<?php

namespace App\Filament\Loggers;

use App\Models\PurchaseRequest;
use App\Filament\Resources\PurchaseRequestResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class PurchaseRequestLogger extends Logger
{
    public static ?string $model = PurchaseRequest::class;

    public static function getLabel(): string | Htmlable | null
    {
        return PurchaseRequestResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('Project')),

                Field::make('received_date')
                    ->label(__('Received Date')),

                Field::make('pr_number')
                    ->label(__('PR Number'))
                    ->money('PHP'),

                Field::make('forward_twg_date')
                    ->label(__('Forwarded to TWG'))
            ])
            ->relationManagers([
                //
            ]);
    }
}
