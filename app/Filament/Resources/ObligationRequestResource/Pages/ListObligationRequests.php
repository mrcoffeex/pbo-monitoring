<?php

namespace App\Filament\Resources\ObligationRequestResource\Pages;

use App\Filament\Resources\ObligationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListObligationRequests extends ListRecords
{
    protected static string $resource = ObligationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
