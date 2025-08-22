<?php

namespace App\Filament\Resources\ImplementationResource\Pages;

use App\Filament\Resources\ImplementationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateImplementation extends CreateRecord
{
    use LogCreateRecord;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected static string $resource = ImplementationResource::class;

}
