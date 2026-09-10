<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Enums\CustomOptions;
use App\Enums\ProcessStage;
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
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Spatie\Activitylog\Models\Activity;

class ProjectMonitoring extends Page
{
    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-monitoring';

    public Project $project;

    /** @var array<class-string, string> */
    public const SUBJECT_LABELS = [
        Project::class => 'Project',
        PreProcurement::class => 'Pre-Procurement',
        PurchaseRequest::class => 'Purchase Request',
        TechnicalWorkingGroup::class => 'Technical Working Group',
        ProcurementControl::class => 'PMO Control',
        PurchaseRequestControl::class => 'PR Control',
        Procurement::class => 'Procurement',
        ObligationRequest::class => 'Obligation Request',
        Implementation::class => 'Implementation',
        Payment::class => 'Payment',
    ];

    public function mount(Project $record): void
    {
        $this->project = $record->load([
            'user',
            'pre_procurements.user',
            'purchase_requests.user',
            'technical_working_groups.user',
            'procurement_controls.user',
            'purchase_request_controls.user',
            'procurements.user',
            'obligation_requests.user',
            'implementations.user',
            'payments.user',
        ]);
    }

    public function getTitle(): string
    {
        return 'Project Monitoring';
    }

    public function getHeading(): string
    {
        return 'Project Monitoring';
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
            Actions\Action::make('activityLog')
                ->label(function (): string {
                    $count = $this->recentActivities->count();

                    return $count > 0 ? "Activity ({$count})" : 'Activity';
                })
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->slideOver()
                ->modalWidth('md')
                ->modalHeading('Project activity')
                ->modalDescription('Recent changes on this project and its related records.')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn (): View => view(
                    'filament.resources.project-resource.pages.partials.project-activity-timeline',
                    [
                        'page' => $this,
                        'groupedActivities' => $this->groupedRecentActivities,
                        'fullLogUrl' => $this->activitiesUrl(),
                    ]
                )),
            Actions\Action::make('edit')
                ->label('Edit Project')
                ->icon('heroicon-o-pencil-square')
                ->color('info')
                ->url(fn (): string => ProjectResource::getUrl('edit', ['record' => $this->project])),
        ];
    }

    /**
     * @return array{
     *     appropriation: float,
     *     allotment: float,
     *     contractAmount: float,
     *     totalPayment: float,
     *     balance: float,
     *     paymentPct: float,
     *     implPct: float,
     *     contractor: string,
     *     statusLabel: string,
     *     statusColor: string,
     *     typeLabels: list<string>,
     *     fundLabels: list<string>
     * }
     */
    #[Computed]
    public function summary(): array
    {
        $payments = $this->project->payments;
        $procurements = $this->project->procurements;
        $implementations = $this->project->implementations;

        $contractAmount = (float) $procurements->sum('contract_amount');
        $totalPayment = (float) $payments->sum('amount');
        $latestImpl = $implementations->sortByDesc(fn (Implementation $item) => $item->date ?? $item->created_at)->first();
        $implPct = ($latestImpl && is_numeric($latestImpl->percentage)) ? (float) $latestImpl->percentage : 0.0;

        $contractors = $procurements->pluck('contractor')->filter()->unique()->values();
        $contractor = match (true) {
            $contractors->isEmpty() => 'No contractor yet',
            $contractors->count() === 1 => (string) $contractors->first(),
            default => $contractors->implode(', '),
        };

        $types = collect((array) $this->project->type)
            ->map(fn ($type): string => CustomOptions::PROJECT_TYPES[$type] ?? (string) $type)
            ->filter()
            ->values()
            ->all();

        $funds = collect((array) $this->project->funds)
            ->map(fn ($fund): string => CustomOptions::FUNDS[$fund] ?? (string) $fund)
            ->filter()
            ->values()
            ->all();

        return [
            'appropriation' => (float) $this->project->appropriation,
            'allotment' => (float) $this->project->allotment,
            'contractAmount' => $contractAmount,
            'totalPayment' => $totalPayment,
            'balance' => (float) $this->project->balance,
            'paymentPct' => $contractAmount > 0 ? round(($totalPayment / $contractAmount) * 100, 1) : 0.0,
            'implPct' => round($implPct, 1),
            'contractor' => $contractor,
            'statusLabel' => CustomOptions::PROJECT_STATUS[$this->project->status] ?? ucfirst((string) $this->project->status),
            'statusColor' => match ($this->project->status) {
                'released' => 'success',
                'unreleased' => 'info',
                'canceled' => 'danger',
                default => 'gray',
            },
            'typeLabels' => $types,
            'fundLabels' => $funds,
        ];
    }

    /**
     * @return list<array{key: string, label: string, count: int, done: bool}>
     */
    #[Computed]
    public function stages(): array
    {
        return collect($this->project->workflowProcessStages())
            ->map(function (ProcessStage $stage): array {
                $count = (int) $this->project->{$stage->relationship()}()->count();

                return [
                    'key' => $stage->monitorKey(),
                    'label' => $stage->label(),
                    'count' => $count,
                    'done' => $count > 0,
                ];
            })
            ->all();
    }

    public function defaultStage(): string
    {
        return 'overview';
    }

    /**
     * @return Collection<int, Activity>
     */
    #[Computed]
    public function recentActivities(): Collection
    {
        return $this->project
            ->projectActivitiesQuery()
            ->limit(20)
            ->get();
    }

    /**
     * @return Collection<string, Collection<int, Activity>>
     */
    #[Computed]
    public function groupedRecentActivities(): Collection
    {
        return $this->recentActivities->groupBy(function (Activity $activity): string {
            $date = $activity->created_at;

            if (! $date) {
                return 'Unknown';
            }

            if ($date->isToday()) {
                return 'Today';
            }

            if ($date->isYesterday()) {
                return 'Yesterday';
            }

            return $date->format('M j, Y');
        });
    }

    public function activityChangeSummary(Activity $activity): ?string
    {
        $properties = $activity->properties;
        $attributes = is_array($properties)
            ? ($properties['attributes'] ?? [])
            : (array) $properties->get('attributes', []);

        if ($attributes === []) {
            $description = trim((string) $activity->description);

            if ($description === '' || $description === (string) $activity->event) {
                return null;
            }

            return $description;
        }

        $entries = collect($attributes)
            ->reject(fn (mixed $value, mixed $key): bool => in_array($key, ['id', 'user_id', 'project_id'], true))
            ->map(function (mixed $value, mixed $key): string {
                $display = is_array($value)
                    ? collect($value)->filter(fn (mixed $item): bool => is_scalar($item))->implode(', ')
                    : (string) $value;

                if ($display === '') {
                    $display = is_array($value) ? json_encode($value) : '—';
                }

                return Str::headline((string) $key).': '.Str::limit($display, 42);
            })
            ->take(2)
            ->values();

        return $entries->isEmpty() ? null : $entries->implode(' · ');
    }

    public function getSubjectLabel(Activity $activity): string
    {
        return self::SUBJECT_LABELS[$activity->subject_type]
            ?? class_basename((string) $activity->subject_type);
    }

    /**
     * @return array{label: string, icon: string, color: string}
     */
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

    public function activitiesUrl(): string
    {
        return ProjectResource::getUrl('activities', ['record' => $this->project]);
    }
}
