<?php

namespace App\Filament\Resources\ImplementationResource\Pages;

use App\Filament\Resources\ImplementationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateImplementation extends CreateRecord
{
    use LogCreateRecord;
    
    protected static string $resource = ImplementationResource::class;
}
