<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\Page;

class ProjectMonitoring extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-monitoring';

    protected static ?string $navigationGroup = 'Menu';

    public Project $project;

    public function mount(Project $record): void
    {
        $this->project = $record->load([
            'purchase_requests',
            'technical_working_groups',
            'purchase_request_controls',
            'procurements',
            'obligation_requests',
            'implementations',
            'payments',
        ]);
    }

    public function getTitle(): string
    {
        return 'Project Monitoring: ' . $this->project->name;
    }
}
