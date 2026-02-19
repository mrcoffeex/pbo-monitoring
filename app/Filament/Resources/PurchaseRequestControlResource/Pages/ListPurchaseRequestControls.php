<?php

namespace App\Filament\Resources\PurchaseRequestControlResource\Pages;

use App\Filament\Resources\PurchaseRequestControlResource;
use App\Models\PurchaseRequestControl;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPurchaseRequestControls extends ListRecords
{
    protected static string $resource = PurchaseRequestControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->createAnother(false)
                ->using(function (array $data): PurchaseRequestControl {

                    $data['user_id'] = Auth::id();

                    return PurchaseRequestControl::create($data);
                }),
        ];
    }
}
