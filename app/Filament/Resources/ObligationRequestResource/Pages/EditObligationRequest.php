<?php

namespace App\Filament\Resources\ObligationRequestResource\Pages;

use App\Filament\Resources\ObligationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Noxo\FilamentActivityLog\Extensions\LogEditRecord;

class EditObligationRequest extends EditRecord
{
    use LogEditRecord;

    protected static string $resource = ObligationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
