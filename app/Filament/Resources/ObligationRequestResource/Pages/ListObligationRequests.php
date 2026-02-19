<?php

namespace App\Filament\Resources\ObligationRequestResource\Pages;

use App\Filament\Resources\ObligationRequestResource;
use App\Models\ObligationRequest;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListObligationRequests extends ListRecords
{
    protected static string $resource = ObligationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->createAnother(false)
                ->using(function (array $data): ObligationRequest {

                    $data['user_id'] = Auth::id();

                    return ObligationRequest::create($data);
                }),
        ];
    }
}
