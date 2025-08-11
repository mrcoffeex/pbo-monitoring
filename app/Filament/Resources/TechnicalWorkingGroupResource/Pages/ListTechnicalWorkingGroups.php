<?php

namespace App\Filament\Resources\TechnicalWorkingGroupResource\Pages;

use App\Filament\Resources\TechnicalWorkingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechnicalWorkingGroups extends ListRecords
{
    protected static string $resource = TechnicalWorkingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
