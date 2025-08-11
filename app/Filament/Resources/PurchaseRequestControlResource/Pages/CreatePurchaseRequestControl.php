<?php

namespace App\Filament\Resources\PurchaseRequestControlResource\Pages;

use App\Filament\Resources\PurchaseRequestControlResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreatePurchaseRequestControl extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = PurchaseRequestControlResource::class;
}
