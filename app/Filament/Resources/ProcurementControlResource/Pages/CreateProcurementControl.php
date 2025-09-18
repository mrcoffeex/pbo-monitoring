<?php

namespace App\Filament\Resources\ProcurementControlResource\Pages;

use App\Filament\Resources\ProcurementControlResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProcurementControl extends CreateRecord
{
    protected static string $resource = ProcurementControlResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
