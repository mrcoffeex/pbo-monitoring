<?php

namespace App\Filament\Resources\PurchaseRequestResource\Pages;

use App\Filament\Resources\PurchaseRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreatePurchaseRequest extends CreateRecord
{
    use LogCreateRecord;

    protected static string $resource = PurchaseRequestResource::class;
}
