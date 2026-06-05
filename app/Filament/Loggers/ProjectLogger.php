<?php

namespace App\Filament\Loggers;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Contracts\Support\Htmlable;
use Noxo\FilamentActivityLog\Loggers\Logger;
use Noxo\FilamentActivityLog\ResourceLogger\Field;
use Noxo\FilamentActivityLog\ResourceLogger\ResourceLogger;
use Spatie\Activitylog\Contracts\Activity;

class ProjectLogger extends Logger
{
    public static ?string $model = Project::class;

    public static function getLabel(): string|Htmlable|null
    {
        return ProjectResource::getModelLabel();
    }

    public function getSubjectRoute(Activity $activity): ?string
    {
        return ProjectResource::getUrl('edit', ['record' => $activity->subject_id]);
    }

    public function getRelationManagerRoute(Activity $activity): ?string
    {
        return $this->getSubjectRoute($activity).'?activeRelationManager=0';
    }

    public static function resource(ResourceLogger $logger): ResourceLogger
    {
        return $logger
            ->fields([
                Field::make('code')
                    ->label(__('Responsibility Center')),

                Field::make('name')
                    ->label(__('Project Name')),

                Field::make('type')
                    ->label(__('Project Type')),

                Field::make('funds')
                    ->label(__('Source of Funds')),

                Field::make('year')
                    ->label(__('Year')),

                Field::make('status')
                    ->label(__('Status')),

                Field::make('appropriation')
                    ->label(__('Appropriation'))
                    ->money('PHP'),

                Field::make('allotment')
                    ->label(__('Allotment'))
                    ->money('PHP'),
            ])
            ->relationManagers([
                //
            ]);
    }
}
