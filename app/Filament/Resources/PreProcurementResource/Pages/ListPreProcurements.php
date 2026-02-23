<?php

namespace App\Filament\Resources\PreProcurementResource\Pages;

use App\Filament\Resources\PreProcurementResource;
use App\Models\PreProcurement;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListPreProcurements extends ListRecords
{
    protected static string $resource = PreProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->createAnother(false)
                ->using(function (array $data): PreProcurement {

                    $data['user_id'] = Auth::id();

                    return PreProcurement::create($data);
                }),
        ];
    }
}
