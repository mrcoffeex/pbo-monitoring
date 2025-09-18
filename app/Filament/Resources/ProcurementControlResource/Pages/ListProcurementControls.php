<?php

namespace App\Filament\Resources\ProcurementControlResource\Pages;

use App\Filament\Resources\ProcurementControlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcurementControls extends ListRecords
{
    protected static string $resource = ProcurementControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
