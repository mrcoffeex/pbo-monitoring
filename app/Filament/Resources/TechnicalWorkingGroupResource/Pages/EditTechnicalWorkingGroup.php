<?php

namespace App\Filament\Resources\TechnicalWorkingGroupResource\Pages;

use App\Filament\Resources\TechnicalWorkingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Noxo\FilamentActivityLog\Extensions\LogEditRecord;

class EditTechnicalWorkingGroup extends EditRecord
{
    use LogEditRecord;

    protected static string $resource = TechnicalWorkingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
