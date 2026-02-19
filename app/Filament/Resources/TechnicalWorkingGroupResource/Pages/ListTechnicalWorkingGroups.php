<?php

namespace App\Filament\Resources\TechnicalWorkingGroupResource\Pages;

use App\Filament\Resources\TechnicalWorkingGroupResource;
use App\Models\TechnicalWorkingGroup;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTechnicalWorkingGroups extends ListRecords
{
    protected static string $resource = TechnicalWorkingGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->modalWidth('md')
                ->createAnother(false)
                ->using(function (array $data): TechnicalWorkingGroup {

                    $data['user_id'] = Auth::id();

                    return TechnicalWorkingGroup::create($data);
                }),
        ];
    }
}
