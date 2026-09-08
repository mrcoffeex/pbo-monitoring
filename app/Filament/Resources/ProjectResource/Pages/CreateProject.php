<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectTypeWorkflow;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Noxo\FilamentActivityLog\Extensions\LogCreateRecord;

class CreateProject extends CreateRecord
{
    use LogCreateRecord;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['workflow_stages'] = ProjectTypeWorkflow::stagesForType(
            Project::normalizeType($data['type'] ?? null),
        );

        return $data;
    }

    protected static string $resource = ProjectResource::class;
}
