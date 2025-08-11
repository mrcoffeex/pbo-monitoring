<?php

namespace App\Filament\Resources\PurchaseRequestControlResource\Pages;

use App\Filament\Resources\PurchaseRequestControlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseRequestControls extends ListRecords
{
    protected static string $resource = PurchaseRequestControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
