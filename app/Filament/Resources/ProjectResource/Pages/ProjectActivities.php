<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Implementation;
use App\Models\ObligationRequest;
use App\Models\Payment;
use App\Models\PreProcurement;
use App\Models\Procurement;
use App\Models\ProcurementControl;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestControl;
use App\Models\TechnicalWorkingGroup;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Noxo\FilamentActivityLog\Pages\Concerns\CanCollapse;
use Noxo\FilamentActivityLog\Pages\Concerns\HasLogger;
use Spatie\Activitylog\Models\Activity;

class ProjectActivities extends Page
{
    use CanCollapse;
    use HasLogger;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-activities';

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public Project $project;

    /** @var array<class-string, string> */
    public const SUBJECT_LABELS = [
        Project::class => 'Project',
        PreProcurement::class => 'Pre-Procurement',
        PurchaseRequest::class => 'Purchase Request',
        TechnicalWorkingGroup::class => 'Technical Working Group',
        ProcurementControl::class => 'Procurement Control',
        PurchaseRequestControl::class => 'PR Control',
        Procurement::class => 'Procurement',
        ObligationRequest::class => 'Obligation Request',
        Implementation::class => 'Implementation',
        Payment::class => 'Payment',
    ];

    public function mount(Project $record): void
    {
        $this->project = $record;
        $this->isCollapsible = true;
        $this->isCollapsed = true;
    }

    public function getTitle(): string
    {
        return 'Activity Log';
    }

    public function getHeading(): string
    {
        return 'Activity Log';
    }

    public function getSubheading(): ?string
    {
        return $this->project->code.' · '.$this->project->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Projects')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(ProjectResource::getUrl('index')),
            Actions\Action::make('monitoring')
                ->label('Monitoring')
                ->icon('heroicon-o-chart-bar')
                ->color('gray')
                ->url(fn (): string => ProjectResource::getUrl('monitoring', ['record' => $this->project])),
            Actions\Action::make('edit')
                ->label('Edit Project')
                ->icon('heroicon-o-pencil-square')
                ->color('info')
                ->url(fn (): string => ProjectResource::getUrl('edit', ['record' => $this->project])),
        ];
    }

    /**
     * @return array{start: ?Carbon, end: ?Carbon, preset: string}
     */
    public function getDateRange(): array
    {
        if (request()->filled('start_date') || request()->filled('end_date')) {
            return [
                'start' => request('start_date') ? Carbon::parse(request('start_date'))->startOfDay() : null,
                'end' => request('end_date') ? Carbon::parse(request('end_date'))->endOfDay() : null,
                'preset' => 'custom',
            ];
        }

        $preset = request('preset', 'all');

        return match ($preset) {
            '7d' => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => now()->endOfDay(),
                'preset' => '7d',
            ],
            '30d' => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay(),
                'preset' => '30d',
            ],
            default => [
                'start' => null,
                'end' => null,
                'preset' => 'all',
            ],
        };
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        ['start' => $startDate, 'end' => $endDate] = $this->getDateRange();

        $query = $this->project->projectActivitiesQuery($startDate, $endDate);

        if ($subject = request('subject')) {
            $query->where('subject_type', $subject);
        }

        if ($event = request('event')) {
            $query->where('event', $event);
        }

        if (request()->filled('causer')) {
            $causer = request('causer');
            if ($causer === 'system') {
                $query->whereNull('causer_id');
            } else {
                $query->where('causer_id', $causer);
            }
        }

        return $query->get();
    }

    /**
     * @return Collection<string, Collection<int, Activity>>
     */
    public function getGroupedActivities(): Collection
    {
        return $this->getActivities()->groupBy(function (Activity $activity): string {
            $date = $activity->created_at ? Carbon::parse($activity->created_at) : now();

            if ($date->isToday()) {
                return 'Today';
            }

            if ($date->isYesterday()) {
                return 'Yesterday';
            }

            return $date->format('l, F j, Y');
        });
    }

    /**
     * @return array{total: int, created: int, updated: int, deleted: int}
     */
    public function getActivityStats(): array
    {
        $activities = $this->getActivities();

        return [
            'total' => $activities->count(),
            'created' => $activities->where('event', 'created')->count(),
            'updated' => $activities->where('event', 'updated')->count(),
            'deleted' => $activities->whereIn('event', ['deleted', 'detached'])->count(),
        ];
    }

    public function getSubjectLabel(Activity $activity): string
    {
        return self::SUBJECT_LABELS[$activity->subject_type]
            ?? class_basename($activity->subject_type);
    }

    public function getEventMeta(?string $event): array
    {
        return match ($event) {
            'created', 'attached' => [
                'label' => 'Created',
                'icon' => 'heroicon-m-plus-circle',
                'color' => 'emerald',
            ],
            'updated' => [
                'label' => 'Updated',
                'icon' => 'heroicon-m-pencil-square',
                'color' => 'blue',
            ],
            'deleted', 'detached' => [
                'label' => 'Deleted',
                'icon' => 'heroicon-m-trash',
                'color' => 'red',
            ],
            'restored' => [
                'label' => 'Restored',
                'icon' => 'heroicon-m-arrow-path',
                'color' => 'amber',
            ],
            default => [
                'label' => ucfirst((string) $event),
                'icon' => 'heroicon-m-bolt',
                'color' => 'gray',
            ],
        };
    }

    /**
     * @return array<string, string>
     */
    public function getSubjectFilterOptions(): array
    {
        $options = [];

        foreach (array_keys($this->project->activitySubjectMap()) as $class) {
            $options[$class] = self::SUBJECT_LABELS[$class] ?? class_basename($class);
        }

        return $options;
    }

    /**
     * @return Collection<int, \App\Models\User>
     */
    public function getCauserFilterOptions(): Collection
    {
        ['start' => $startDate, 'end' => $endDate] = $this->getDateRange();

        return $this->project
            ->projectActivitiesQuery($startDate, $endDate)
            ->get()
            ->pluck('causer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    public function activitiesUrl(array $params = []): string
    {
        $query = array_filter($params, fn ($value) => $value !== null && $value !== '');

        $url = ProjectResource::getUrl('activities', ['record' => $this->project]);

        if ($query === []) {
            return $url;
        }

        return $url.'?'.http_build_query($query);
    }

    public function hasActiveFilters(): bool
    {
        return request()->hasAny(['subject', 'event', 'causer', 'start_date', 'end_date'])
            || (request('preset', 'all') !== 'all');
    }
}
