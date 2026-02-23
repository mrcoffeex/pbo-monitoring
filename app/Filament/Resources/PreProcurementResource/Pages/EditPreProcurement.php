<?php

namespace App\Filament\Resources\PreProcurementResource\Pages;

use App\Filament\Resources\PreProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPreProcurement extends EditRecord
{
    protected static string $resource = PreProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
