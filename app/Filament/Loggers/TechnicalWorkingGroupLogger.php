<?php

namespace App\Filament\Loggers;

use App\Models\TechnicalWorkingGroup;
use App\Filament\Resources\TechnicalWorkingGroupResource;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\RelationManager;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;

class TechnicalWorkingGroupLogger extends Logger
{
    public static ?string $model = TechnicalWorkingGroup::class;

    public static function getLabel(): string | Htmlable | null
    {
        return TechnicalWorkingGroupResource::getModelLabel();
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('center.name')
                    ->label(__('Project')),

                Field::make('review_date')
                    ->label(__('TWG Review Date')),

                Field::make('review_remarks')
                    ->label(__('Remarks')),

                Field::make('controlled_date')
                    ->label(__('TWG Controlled Date')),

                Field::make('abc')
                    ->label(__('Approved Budget for the Contract'))
                    ->money('PHP'),

                Field::make('remarks')
                    ->label(__('Remarks')),
            ])
            ->relationManagers([
                //
            ]);
    }
}
