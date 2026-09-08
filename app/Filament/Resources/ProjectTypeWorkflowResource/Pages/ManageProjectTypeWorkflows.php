<?php

namespace App\Filament\Resources\ProjectTypeWorkflowResource\Pages;

use App\Filament\Resources\ProjectTypeWorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectTypeWorkflows extends ManageRecords
{
    protected static string $resource = ProjectTypeWorkflowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
