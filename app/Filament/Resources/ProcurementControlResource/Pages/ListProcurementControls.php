<?php

namespace App\Filament\Resources\ProcurementControlResource\Pages;

use App\Filament\Resources\ProcurementControlResource;
use App\Models\ProcurementControl;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListProcurementControls extends ListRecords
{
    protected static string $resource = ProcurementControlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->createAnother(false)
                ->using(function (array $data): ProcurementControl {

                    $data['user_id'] = Auth::id();

                    return ProcurementControl::create($data);
                }),
        ];
    }
}
