<?php

namespace App\Filament\Resources\ObligationRequestResource\Pages;

use App\Filament\Resources\ObligationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateObligationRequest extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = ObligationRequestResource::class;
}
