<?php

namespace App\Filament\Resources\TechnicalWorkingGroupResource\Pages;

use App\Filament\Resources\TechnicalWorkingGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateTechnicalWorkingGroup extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = TechnicalWorkingGroupResource::class;
}
