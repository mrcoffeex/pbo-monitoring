<?php

namespace App\Filament\Resources\ProcurementControlResource\Pages;

use App\Filament\Resources\ProcurementControlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProcurementControl extends EditRecord
{
    protected static string $resource = ProcurementControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
