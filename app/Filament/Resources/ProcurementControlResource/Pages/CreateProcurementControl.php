<?php

namespace App\Filament\Resources\ProcurementControlResource\Pages;

use App\Filament\Resources\ProcurementControlResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateProcurementControl extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = ProcurementControlResource::class;
}
